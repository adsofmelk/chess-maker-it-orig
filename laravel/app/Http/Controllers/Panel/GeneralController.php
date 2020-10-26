<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Partidas as Game;
use App\User;

class GeneralController extends Controller
{
    public function indexPanel()
    {
        $countGames = Game::count('id');
        
        $scoremax = User::max('rating');
        $gamesmax = User::max('game_win');
        
        $userScore = User::select('id', 'name', 'email', 'rating')->where('rating', $scoremax)->get();
        $userGameWin = User::select('id', 'name', 'email', 'rating')->where('game_win', $gamesmax)->get();
        
        $data = new \stdClass();
        
        $data->number_games = $countGames;
        $data->maxscores = $userScore;
        $data->wingames = $userGameWin;
        $data->score_max = $scoremax;
        $data->games_win = $gamesmax;
        
        
        return view('panel.index', [
            'data' => $data,
        ]);
    }
}
