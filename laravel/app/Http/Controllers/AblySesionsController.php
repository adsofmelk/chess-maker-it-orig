<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\SessionesAbly as AblySession;
use App\HallsWait as HallsWait;
use App\Partidas as Game;
use App\GamesTemp as PreGame;

use App\Traits\ChessTrait;

use Illuminate\Support\Str;
use Session;
use Ably;
use Illuminate\Support\Facades\Log;

class AblySesionsController extends Controller
{
    use ChessTrait;

    // return main view of game tab
    public function playInitial(Request $request)
    {
        $request->session()->forget('yetresponse');
        $request->session()->forget('gameBoard');
        PreGame::where('user1', $request->user()->id)->orWhere('user2', $request->user()->id)->delete();
        HallsWait::where('user', $request->user()->id)->delete();
        // $request->session()->put('pregamereto', true);
        // $request->session()->forget('pregamereto');

        if (!$request->session()->has('pregamereto')) {
            Game::where(function ($query) use ($request) {
                $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
            })->where('status', '1')->delete();
        }

        return view('playgame', ['preGame'=>$request->session()->has('pregamereto')]);
    }

    /**
     * Authenticate with ably.io and it generate token for game tab
     */
    public function authAbly(Request $request)
    {
        $begin = $request->input('begin');
        $aTokenParams = [];

        // add channel of game to token
        if ($begin) {
            // $game = Game::where('user1',$request->user()->id)->orWhere('user2', $request->user()->id)->where('status','1')->first();
            // $game = Game::where('user1',$request->user()->id)->orWhere('user2', $request->user()->id)->where('status','1')->first();
            $game = Game::where(function ($query) use ($request) {
                $query->where('user1', $request->user()->id)
                      ->orWhere('user2', $request->user()->id);
            })->where('status', '1')->where('type', 2)->first();

            HallsWait::where('user', $request->user()->id)->delete();

            $nChannelGame = $game->channel_ably;
            $distribution = $game->distribution;
            $gameboard = $this->buildPieces($distribution);

            $request->session()->put('gameBoard', $gameboard['pieces']);
            $request->session()->put('piecesmap', $gameboard['piecesMap']);
            $request->session()->put('piecesnames', $gameboard['piecesNames']);

            $request->session()->put('gameBoardTime', $game->created_at);
            $request->session()->put('idgame', $game->id);
            $request->session()->forget('pregamereto');

            $aTokenParams = [
                'capability' => [
                    $request->session()->get('channel_user') => ['publish', 'subscribe','presence'],
                    'damos' => ['publish', 'subscribe','presence'],
                    $nChannelGame => ['publish', 'subscribe','presence'],
                ],
                // 'clientId' => strval(time())
                'clientId' => encrypt($request->user()->email)
            ];

        // return response()->json(Ably::auth()->createTokenRequest($aTokenParams)->toArray());
            // return response()->json(Ably::auth()->authorize($aTokenParams)->toArray());
        } else {
            $channelBefore = AblySession::where('user_id', $request->user()->id)->first();

            if ($channelBefore != null) {
                Ably::channel($channelBefore->channel_ably)->publish('private_user', ['status' => 300, 'message' => 'Se abrio la pagina para jugar en otra pestaña' ,]);

                $channelBefore->delete();
            }


            $nameChannel = 'channel_'.Str::random(10);

            AblySession::create([
                'channel_ably' => $nameChannel,
                'user_id' => $request->user()->id
            ]);

            $request->session()->put('channel_user', $nameChannel);

            $aTokenParams = [
                'capability' => [
                    $nameChannel => ['publish', 'subscribe','presence'],
                    'damos' => ['publish', 'subscribe','presence'],
                ],
                // 'clientId' => strval(time())
                'clientId' => encrypt($request->user()->email)
            ];

            // return response()->json(Ably::auth()->createTokenRequest($aTokenParams)->toArray());
        }

        // return Ably::auth()->requestToken([ 'clientId' => 'client123', ]);
        return response()->json(Ably::auth()->createTokenRequest($aTokenParams)->toArray());
    }

    /**
     * Authenticate with ably.io and it generate token for game tab
     */
    public function authAblyNormal(Request $request)
    {
        $aTokenParams = [];

        $channelBefore = AblySession::where('user_id', $request->user()->id)->first();

        if ($channelBefore != null) {
            Ably::channel($channelBefore->channel_ably)->publish('private_user', ['status' => 300, 'message' => 'Se abrio la pagina para jugar en otra pestaña' ,]);

            $channelBefore->delete();
        }

        $nameChannel = 'channel_'.Str::random(10);

        AblySession::create([
            'channel_ably' => $nameChannel,
            'user_id' => $request->user()->id
        ]);

        $request->session()->put('channel_user', $nameChannel);

        $aTokenParams = [
            'capability' => [
                $nameChannel => ['publish', 'subscribe','presence'],
                'damos' => ['publish', 'subscribe','presence'],
                'hall_wait' => ['publish', 'subscribe','presence'],
            ],
            // 'clientId' => strval(time())
            'clientId' => encrypt($request->user()->email)
        ];

        // return Ably::auth()->requestToken([ 'clientId' => 'client123', ]);
        return response()->json(Ably::auth()->createTokenRequest($aTokenParams)->toArray());
    }

    /**
     * Get name of channel active of player
     * @param  Request $request [description]
     * @return [json]           [name of channel]
     */
    public function getNameChannel(Request $request)
    {
        $nameChannel = AblySession::where('user_id', $request->user()->id)->first();

        return response()->json([
            'status'      => 200,
            'nameChannel' => $nameChannel->channel_ably,
        ]);
    }

    // find player
    public function findPlayer(Request $request)
    {
        $hall_user = HallsWait::select('channel', 'id', 'user')->where('user', '<>', $request->user()->id)->first();

        if ($hall_user == null) {
            HallsWait::where('user', $request->user()->id)->delete();

            HallsWait::create([
                'user'    => $request->user()->id,
                'channel' => $request->session()->get('channel_user'),
            ]);

            return response()->json([
                'status'  => 1,
                'message' => 'Estas en cola',
            ]);
        } else {
            $otherUser = Ably::channel($hall_user->channel)->presence->get();

            $nChannelGame = Str::random(10);
            $distribution = $this->getDistribution();

            if (!empty($otherUser->items)) {
                $whoBegin = [$hall_user->user,$request->user()->id];
                shuffle($whoBegin);

                $gameboard = $this->buildPieces($distribution);

                $request->session()->put('gameBoard', $gameboard['pieces']);
                $request->session()->put('piecesmap', $gameboard['piecesMap']);
                $request->session()->put('piecesnames', $gameboard['piecesNames']);

                $game = Game::create([
                    'user1'        => $hall_user->user,
                    'user2'        => $request->user()->id,
                    'status'       => '1',
                    'distribution' => $distribution,
                    'who_begin'    => $whoBegin[0],
                    'channel_ably' => $nChannelGame,
                    'board_data'   => json_encode($gameboard),
                    // 'channel_ably' => $hall_user->channel,
                ]);

                $meBegin  = ($whoBegin[0]==$request->user()->id) ? true : false;
                $youBegin = ($whoBegin[0]==$hall_user->user) ? true : false;

                Ably::channel($hall_user->channel)->publish('private_user', [
                    'status'          => 201,
                    'message'         => 'empezar partida',
                    'nameChannelGame' => $nChannelGame,
                    'distribution'    => $distribution,
                    'me_begin'        => $youBegin,
                    'time'            => $game->created_at,
                ]);

                $hall_user->delete();

                return response()->json([
                    'status'  	   => 2,
                    'message' 	   => 'Conectando...',
                    'channel' 	   => $nChannelGame,
                    'distribution' => $distribution,
                    'me_begin'     => $meBegin,
                    'time'         => $game->created_at,
                ]);
            } else {
                $hall_user->delete();
                HallsWait::create([
                    'user'    => $request->user()->id,
                    'channel' => $request->session()->get('channel_user'),
                ]);

                return response()->json([
                    'status'  => 1,
                    'message' => 'Estas en cola',
                ]);
            }
        }
    }

    public function checkGame(Request $request)
    {
        $request->session()->put('pregamereto', true);
        return response()->json([
            'status' => 'correcto'
        ]);
    }

    public function test(Request $request)
    {
        Log::info($request->user()->email);
        // $userDecrypt = decrypt($request->input('clientId'));

        return response()->json([
            'user' => 'sss'
            // 'user' => $userDecrypt
        ]);

        // Ably::channel('damos')->publish('enter', 'desde ruta test');
        // Ably::channel('damos')->publish('greeting', ['status' => 300, 'message' => 'recargar pestaña' ,]);
        // $users = Ably::channel('channel_4Uvfi1C4Qi')->presence->get();

        // if (empty($users->items)) {
        //     return response()->json([
        //         'items' => 0
        //     ]);
        // } else {
        //     return response()->json([
        //         'items' => $users->items,
        //         'email' => decrypt($users->items[0]->clientId)
        //     ]);
        // }

        // return response()->json($users);
    }
}
