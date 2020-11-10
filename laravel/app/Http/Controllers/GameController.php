<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Scores;
use App\HallsWait as HallsWait;
use App\Partidas as Game;
use App\GamesTemp as PreGame;
use App\SessionesAbly as AblySession;
use Illuminate\Support\Str;
use Ably\Laravel\Facades\Ably;

use App\Traits\ChessTrait;
use App\Traits\HelperGame;

class GameController extends Controller
{
    use ChessTrait, HelperGame;
    
    public function viewPieces(Request $request)
    {
        $arr = ['koala', 'tiger', 'tiger', 'diego', 'polar', 'tiger'];
        // $search = array_search('tiger', $arr);
        $search = array_keys($arr, 'tiger');
        
        return response()->json([
            'search' => $search,
            'typeof' => gettype($search),
            'diff' => array_diff($arr, ['tiger']),
        ]);
    }
    
    public function moveMachine(Request $request)
    {
        $game = Game::select('id', 'moves', 'board_data', 'who_begin')->where('id',
            $request->session()->get('idgame'))->first();
        
        if ($game != null) {
            $color = ($game->who_begin == 0) ? 'white' : 'black';
            
            return $this->calcMovesMachine($request, $game, $color);
        }
        
        return view('errors.404');
    }
    
    public function playMachine(Request $request)
    {
        Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->delete();
        
        $distributionLast = '';
        $whoBegin = [$request->user()->id, 0];
        
        if ($request->session()->has('pregameMachine')) {
            // obtener la ultima parttida
            $gameLast = Game::where('id', $request->session()->get('idgame'))->first();
            
            $distributionLast = $gameLast->distribution;
            
            if ($request->user()->id == $gameLast->who_begin) {
                $whoBegin = [0, $request->user()->id];
            }
            
            $request->session()->forget('pregameMachine');
        } else {
            shuffle($whoBegin);
        }
        
        $distribution = $this->getDistribution($distributionLast);
        $gameboard = $this->buildPieces($distribution);
        
        $game = Game::create([
            'user1' => 0,
            'user2' => $request->user()->id,
            'status' => '1',
            'distribution' => $distribution,
            'who_begin' => $whoBegin[0],
            'board_data' => json_encode($gameboard),
            'type' => 1,
        ]);
        
        $request->session()->put('gameBoard', $gameboard['pieces']);
        $request->session()->put('piecesmap', $gameboard['piecesMap']);
        $request->session()->put('piecesnames', $gameboard['piecesNames']);
        $request->session()->put('gameBoardTime', $game->created_at);
        $request->session()->put('idgame', $game->id);
        $request->session()->forget('pregamereto');
        
        $meBegin = ($whoBegin[0] == $request->user()->id) ? true : false;
        
        return view('playmachine', [
            'preGame' => false,
            'distribution' => $distribution,
            'begin' => $meBegin,
            'time_game' => $game->created_at,
        ]);
    }
    
    /**
     * @param Request $request
     * @param $game
     * @param $colorMachine
     * @return \Illuminate\Http\JsonResponse
     * action en response => 1 = move, 2 = machine win, 3 = player win
     * piece => piece to move
     * box => location to move piece
     */
    protected function calcMovesMachine(Request $request, $game, $colorMachine)
    {
        $boardata = json_decode($game->board_data, true);
        $pieces = $boardata['pieces'];
        $piecesMap = $boardata['piecesMap'];
        $piecesNames = $boardata['piecesNames'];
        $pieceBallLoc = $piecesMap[0];
        
        $movesPieces = $this->getMovesPieces($pieces, $piecesMap);
        
        // pieces white or black
        $getPieces = $this->getMyPieces($colorMachine, $piecesMap);
        $myPieces = $getPieces['myPieces'];
        $youPieces = $getPieces['youPieces'];
        $myPiecesLoc = $getPieces['myPiecesLoc'];
        $youPiecesLoc = $getPieces['youPiecesLoc'];
        
        $myAttack = [];
        $allMyMoves = [];
        
        $youAttack = [];
        $youPieceJaque = '';
        $allYouMoves = [];
        $jaqueYou = false;
        
        // calcula si hay jaque directo y los ataques correctos de las piezas de la máquina
        // -----0077700----- Posible mejora => mover los movimientos invalidos de moves_attack a moves del array de piece
        foreach ($myPieces as $piece) {
            $movesAttack = $movesPieces[$piece]['moves_attack'];
            
            $movesPieces[$piece]['allmoves'] = $movesPieces[$piece]['moves'];
            $movesPieces[$piece]['movesdefense'] = [];
            $movesPieces[$piece]['attack'] = [];
            $movesPieces[$piece]['defense'] = [];
            $movesPieces[$piece]['locationmap'] = $piecesMap[$pieces[$piece]['map']];
            
            $allMyMoves = array_merge($allMyMoves, $movesPieces[$piece]['moves']);
            
            foreach ($movesAttack as $move) {
                if ($move == $pieceBallLoc) {
                    
                    $locationTemp = str_split($move);
                    $locationFormatTemp = [
                        'row' => $locationTemp[0],
                        'col' => $locationTemp[1],
                    ];
                    
                    $this->updateDataGame($request, $game, [
                        'piece' => $piece,
                        'location' => $locationFormatTemp,
                    ]);
                    
                    return response()->json([
                        'status' => 200,
                        'action' => 2,
                        'message' => 'The machine win!!!',
                        'box' => $move,
                        'piece' => $piece,
                        'moves_pieces' => $movesPieces,
                    ]);
                }
                
                if (array_search($move, $youPiecesLoc) !== false) {
                    $movesPieces[$piece]['attack'][$piecesNames[array_search($move, $piecesMap)]] = $move;
                } else {
                    $movesPieces[$piece]['allmoves'][] = $move;
                    $movesPieces[$piece]['movesdefense'][] = $move;
                    $movesPieces[$piece]['defense'][$piecesNames[array_search($move, $piecesMap)]] = $move;
                }
                
                $allMyMoves[] = $move;
            }
            
            if ( ! empty($movesPieces[$piece]['attack'])) {
                $myAttack[$piece] = $movesPieces[$piece]['attack'];
            }
        }
        
        // Buscar si hay jaque directo del jugador y los ataques correctos
        // -----0077700----- Posible mejora => mover los movimientos invalidos de moves_attack a moves del array de piece
        foreach ($youPieces as $piece) {
            $movesAttack = $movesPieces[$piece]['moves_attack'];
            $movesPieces[$piece]['allmoves'] = $movesPieces[$piece]['moves'];
            $movesPieces[$piece]['movesdefense'] = [];
            $movesPieces[$piece]['attack'] = [];
            $movesPieces[$piece]['defense'] = [];
            $movesPieces[$piece]['locationmap'] = $piecesMap[$pieces[$piece]['map']];
            
            $allYouMoves = array_merge($allYouMoves, $movesPieces[$piece]['moves']);
            
            foreach ($movesAttack as $move) {
                if (array_search($move, $myPiecesLoc) !== false) {
                    $movesPieces[$piece]['attack'][$piecesNames[array_search($move, $piecesMap)]] = $move;
                } else {
                    $movesPieces[$piece]['allmoves'][] = $move;
                    $movesPieces[$piece]['movesdefense'][] = $move;
                    $movesPieces[$piece]['defense'][$piecesNames[array_search($move, $piecesMap)]] = $move;
                }
                
                if ($move == $pieceBallLoc) {
                    $jaqueYou = true;
                    $youPieceJaque = $piece;
                }
                
                $allYouMoves[] = $move;
            }
            
            if ( ! empty($movesPieces[$piece]['attack'])) {
                $youAttack[$piece] = $movesPieces[$piece]['attack'];
            }
        }
        
        // agregar ataques que recibe cada pieza y quienes defienden cada pieza
        foreach ($movesPieces as $piece => $data) {
            $movesPieces[$piece]['who_me_attack'] = [];
            $movesPieces[$piece]['who_me_defiende'] = [];
            $locationOrigin = $piecesMap[$pieces[$piece]['map']];
            
            foreach ($movesPieces as $pieceattack => $data2) {
                $attackTemp = $data2['attack'];
                $defenceTemp = $data2['defense'];
                
                if (array_search($locationOrigin, $attackTemp)) {
                    $movesPieces[$piece]['who_me_attack'][$pieceattack] = $data2['locationmap'];
                }
                
                if (array_search($locationOrigin, $defenceTemp)) {
                    $movesPieces[$piece]['who_me_defiende'][$pieceattack] = $data2['locationmap'];
                }
            }
        }
        
        // si el jugador tiene jaque
        if ($jaqueYou) {
            // obtener movimiento para defender del jaque
            $response = $this->getMoveWhenIsJaquePlayer([
                'pieces' => $pieces,
                'movesPieces' => $movesPieces,
                'pieceJaque' => $youPieceJaque,
                'piecesMachine' => $myPieces,
                'attackMachine' => $myAttack,
                'allMovesMachine' => $allMyMoves,
                'locations' => $piecesMap,
            ]);
            
            // update boardata of game
            $this->updateDataGame($request, $game, [
                'piece' => $response['piece'],
                'location' => $response['location'],
            ]);
            
            return response()->json([
                'status' => 200,
                'action' => $response['action'],
                'message' => $response['message'],
                'piece' => $response['piece'],
                'box' => $response['box'],
                'line' => $response['line'],
            ]);
            
        }
        
        $capturesNotDefende = [];
        $moreAttackSamePiece = '';
        $frecuenciaAttackSamePiece = 0;
        $bestPieceToMove = [];
        $typeBestPieceToMove = [];
        $boxToBestMove = '';
        // si hay captura encontrar cual da jaque y nadie la ataca
        if ( ! empty($myAttack)) {
            foreach ($myAttack as $piece => $attack) {
                $pieceToMove = $pieces[$piece];
                
                foreach ($attack as $pieceAttack => $move) {
                    $piecesLocTemp = $piecesMap;
                    $piecesLocTemp[$pieceToMove['map']] = $move;
                    
                    $locationTemp = str_split($move);
                    $locationFormatTemp = [
                        'row' => $locationTemp[0],
                        'col' => $locationTemp[1],
                    ];
                    $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $locationFormatTemp, $pieceBallLoc, $piecesLocTemp);
                    
                    $isNotCapture = array_search($move, $allYouMoves);
                    $countDefense = count(array_keys($allYouMoves, $move));
                    
                    // captura la pieza porque hace jaque y nadie la ataca
                    if ($doJaqueAfterMove && $isNotCapture === false) {
                        $this->updateDataGame($request, $game, [
                            'piece' => $piece,
                            'location' => $locationFormatTemp,
                        ]);
                        
                        return response()->json([
                            'status' => 200,
                            'action' => 1,
                            'message' => 'Mover la pieza porque captura, hace jaque y nadie la captura',
                            'piece' => $piece,
                            'box' => $move,
                            'line' => __LINE__,
                        ]);
                    } elseif ($isNotCapture === false) {
                        // capturas que no estan defendidas
                        $capturesNotDefende[$piece] = $move;
                    }
                    
                    if ($moreAttackSamePiece != $pieceAttack && $frecuenciaAttackSamePiece == 0 && $countDefense < 2) {
                        $moreAttackSamePiece = $pieceAttack;
                        $frecuenciaAttackSamePiece++;
                        $bestPieceToMove[] = $piece;
                        $typeBestPieceToMove[] = $pieceToMove['type'];
                    } elseif ($moreAttackSamePiece == $pieceAttack) {
                        $frecuenciaAttackSamePiece++;
                        $bestPieceToMove[] = $piece;
                        $typeBestPieceToMove[] = $pieceToMove['type'];
                        $boxToBestMove = $move;
                    }
                }
            }
        }
        
        // buscar jaque en la siguiente jugada y que no este atacada
        foreach ($myPieces as $piece) {
            $moves = $movesPieces[$piece]['moves'];
            $pieceToMove = $pieces[$piece];
            
            foreach ($moves as $move) {
                $piecesLocTemp = $piecesMap;
                $piecesLocTemp[$pieceToMove['map']] = $move;
                $locationTemp = str_split($move);
                $locationFormatTemp = [
                    'row' => $locationTemp[0],
                    'col' => $locationTemp[1],
                ];
                $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $locationFormatTemp, $pieceBallLoc, $piecesLocTemp);
                
                $isNotCapture = array_search($move, $allYouMoves);
                
                // mueve la pieza porque hace jaque y nadie la ataca
                if ($doJaqueAfterMove && $isNotCapture === false) {
                    $this->updateDataGame($request, $game, [
                        'piece' => $piece,
                        'location' => $locationFormatTemp,
                    ]);
                    
                    return response()->json([
                        'status' => 200,
                        'action' => 1,
                        'message' => 'Mover la pieza porque hace jaque y nadie la captura',
                        'piece' => $piece,
                        'box' => $move,
                        'line' => __LINE__,
                    ]);
                }
            }
        }
        
        // buscar jaque del jugador en la siguiente jugada y matarlo
        foreach ($youPieces as $piece) {
            $moves = $movesPieces[$piece]['moves'];
            $pieceToMove = $pieces[$piece];
            
            foreach ($moves as $move) {
                $piecesLocTemp = $piecesMap;
                $piecesLocTemp[$pieceToMove['map']] = $move;
                $locationTemp = str_split($move);
                $locationFormatTemp = [
                    'row' => $locationTemp[0],
                    'col' => $locationTemp[1],
                ];
                $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $locationFormatTemp,
                    $pieceBallLoc, $piecesLocTemp);
                
                $isCapture = array_search($move, $capturesNotDefende);
                
                // se puede capturar la pieza que da jaque en el siguiente movimiento y no esta defendida
                if ($doJaqueAfterMove && $isCapture !== false && empty($movesPieces[$piece]['who_me_defiende'])) {
                    $this->updateDataGame($request, $game, [
                        'piece' => $isCapture,
                        'location' => $pieceToMove['location'],
                    ]);
                    
                    return response()->json([
                        'status' => 200,
                        'action' => 1,
                        'message' => 'Mover la pieza porque el jugador hace jaque y nadie la captura',
                        'piece' => $isCapture,
                        'box' => $capturesNotDefende[$isCapture],
                        'line' => __LINE__,
                    ]);
                }
            }
        }
        
        // si no hay jaque del contrario en el siguiente turno matar la pieza que no este defendida
        foreach ($capturesNotDefende as $piece => $move) {
            $locationTemp = str_split($move);
            $locationFormatTemp = [
                'row' => $locationTemp[0],
                'col' => $locationTemp[1],
            ];
            $this->updateDataGame($request, $game, [
                'piece' => $piece,
                'location' => $locationFormatTemp,
            ]);
            
            return response()->json([
                'status' => 200,
                'action' => 1,
                'message' => 'Mover la pieza porque nadie la defiende',
                'piece' => $piece,
                'box' => $move,
                'line' => __LINE__,
            ]);
        }
        
        // capturar ficha porque hay mas defensa propia que el contrario
        if ($frecuenciaAttackSamePiece > 1) {
            $bestScore = 0;
            $movePiece = '';
            $locationTemp = str_split($boxToBestMove);
            $locationFormatTemp = [
                'row' => $locationTemp[0],
                'col' => $locationTemp[1],
            ];
            
            foreach ($bestPieceToMove as $piece) {
                $pieceToMove = $pieces[$piece];
                $piecesLocTemp = $piecesMap;
                $piecesLocTemp[$pieceToMove['map']] = $boxToBestMove;
                
                $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $locationFormatTemp,
                    $pieceBallLoc, $piecesLocTemp);
                
                if ( ! $doJaqueAfterMove) {
                    $bestScore = 3;
                    $movePiece = $piece;
                } elseif ($bestScore < 2) {
                    $bestScore = 1;
                    $movePiece = $piece;
                }
            }
            
            $this->updateDataGame($request, $game, [
                'piece' => $movePiece,
                'location' => $locationFormatTemp,
            ]);
            
            return response()->json([
                'status' => 200,
                'action' => 1,
                'message' => 'Mover la pieza porque hay mas piezas que capturan',
                'piece' => $movePiece,
                'box' => $boxToBestMove,
                'line' => __LINE__,
            ]);
        }
        
        // no hay jaque pero si hay ataques que puede realizar el jugador, salvar la primera pieza
        if ( ! empty($youAttack)) {
            foreach ($youAttack as $piece => $attack) {
                foreach ($attack as $pieceAttack => $move) {
                    $boxesValid = array_diff($movesPieces[$pieceAttack]['moves'], $allYouMoves);
                    
                    if (count($boxesValid) > 0) {
                        $key0 = key($boxesValid);
                        $move0 = $boxesValid[$key0];
                        $locationTemp = str_split($move0);
                        $locationFormatTemp = [
                            'row' => $locationTemp[0],
                            'col' => $locationTemp[1],
                        ];
                        $this->updateDataGame($request, $game, [
                            'piece' => $pieceAttack,
                            'location' => $locationFormatTemp,
                        ]);
                        
                        return response()->json([
                            'status' => 200,
                            'action' => 1,
                            'message' => 'Mover la pieza para salvar que la ataquen',
                            'piece' => $pieceAttack,
                            'box' => $move0,
                            'line' => __LINE__,
                        ]);
                    }
                }
            }
        }
        
        // no hay jaque pero si hay ataques que puede realizar la máquina
        if ( ! empty($myAttack)) {
            foreach ($myAttack as $piece => $attack) {
                $pieceToMove = $pieces[$piece];
                
                foreach ($attack as $pieceAttack => $move) {
                    $piecesLocTemp = $piecesMap;
                    $piecesLocTemp[$pieceToMove['map']] = $move;
                    
                    $locationTemp = str_split($move);
                    $locationFormatTemp = [
                        'row' => $locationTemp[0],
                        'col' => $locationTemp[1],
                    ];
                    $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $locationFormatTemp,
                        $pieceBallLoc, $piecesLocTemp);
                    
                    $isNotCapture = array_search($move, $allYouMoves);
                    $countDefense = count(array_keys($allYouMoves, $move));
                    
                    // captura la pieza porque hace jaque y nadie la ataca
                    if ($isNotCapture === false) {
                        $this->updateDataGame($request, $game, [
                            'piece' => $piece,
                            'location' => $locationFormatTemp,
                        ]);
                        
                        return response()->json([
                            'status' => 200,
                            'action' => 1,
                            'message' => 'Mover la pieza porque captura, y nadie la captura',
                            'piece' => $piece,
                            'box' => $move,
                            'line' => __LINE__,
                        ]);
                    } elseif ( ! empty($movesPieces[$piece]['who_me_defiende'])) {
                        // capturas que me defienden
                        $this->updateDataGame($request, $game, [
                            'piece' => $piece,
                            'location' => $locationFormatTemp,
                        ]);
                        
                        return response()->json([
                            'status' => 200,
                            'action' => 1,
                            'message' => 'Mover la pieza porque captura, y es defendida',
                            'piece' => $piece,
                            'box' => $move,
                            'line' => __LINE__,
                        ]);
                    }
                }
            }
        }
        
        // no hay ataque de nadie
        foreach ($myPieces as $piece) {
            $moves = $movesPieces[$piece]['moves'];
            $pieceToMove = $pieces[$piece];
            
            foreach ($moves as $move) {
                $piecesLocTemp = $piecesMap;
                $piecesLocTemp[$pieceToMove['map']] = $move;
                $locationTemp = str_split($move);
                $locationFormatTemp = [
                    'row' => $locationTemp[0],
                    'col' => $locationTemp[1],
                ];
                
                $isNotCapture = array_search($move, $allYouMoves);
                
                // mueve la pieza porque hace jaque y nadie la ataca
                if ($isNotCapture === false) {
                    $this->updateDataGame($request, $game, [
                        'piece' => $piece,
                        'location' => $locationFormatTemp,
                    ]);
                    
                    return response()->json([
                        'status' => 200,
                        'action' => 1,
                        'message' => 'Mover la pieza a casilla no atacada',
                        'piece' => $piece,
                        'box' => $move,
                        'line' => __LINE__,
                    ]);
                }
            }
        }
    }
    
    /**
     * Update the data of game
     * @param Request $request
     * @param Game $game
     * @param $params array(piece, location => [row,  col])
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateDataGame(Request $request, Game $game, $params)
    {
        $boardata = json_decode($game->board_data, true);
        $pieces = $boardata['pieces'];
        $piecesMap = $boardata['piecesMap'];
        $piecesNames = $boardata['piecesNames'];
        $pieceMove = $params['piece'];
        $locationMove = $params['location'];
        $location = $locationMove['row'].$locationMove['col'];
        $pieceDie = array_search($location, $piecesMap);
        
        if ($pieceDie != '') {
            $piecesMap[$pieceDie] = '99';
            $pieces[$piecesNames[$pieceDie]]['location'] = [
                'row' => 9,
                'col' => 9,
            ];
        }
        
        $pieces[$pieceMove]['location'] = $locationMove;
        $piecesMap[$pieces[$pieceMove]['map']] = $location;
        
        $request->session()->put('gameBoard', $pieces);
        $request->session()->put('piecesmap', $piecesMap);
        $request->session()->put('piecesnames', $piecesNames);
        
        $boardata['pieces'] = $pieces;
        $boardata['piecesMap'] = $piecesMap;
        $moves = $game->moves + 1;
        
        $game->board_data = json_encode($boardata);
        $game->moves = $moves;
        $game->update();
        
        if ($moves == 16) {
            return response()->json([
                'status' => 0,
                'validate' => 'Es un empate',
            ]);
        }
    }
    
    public function getFirstMove(Request $request)
    {
        $game = Game::select('id', 'moves', 'board_data', 'who_begin')->where('id', $request->session()->get('idgame'))->first();
        
        if ($game != null) {
            if ($game->moves == 0) {
                $color = ($game->who_begin == 0) ? 'white' : 'black';
                
                return $this->calcMovesMachine($request, $game, $color);
            }
        }
        
        return view('errors.404');
    }
    
    public function validateMove(Request $request)
    {
        $game = Game::select('id', 'moves', 'board_data')->where('id', $request->session()->get('idgame'))->first();
        
        if ($game == null) {
            return view('errors.404');
        }
        
        $boardata = json_decode($game->board_data, true);
        $pieces = $boardata['pieces'];
        $piecesMap = $boardata['piecesMap'];
        $piecesNames = $boardata['piecesNames'];
        $pieceMove = $request->input('piece');
        $locationMove = $request->input('location');
        
        $checkMove = $this->checkMove([
            'locInitial' => $pieces[$pieceMove]['location'],
            'locFinal' => $locationMove,
            'type' => $pieces[$pieceMove]['type'],
            'pieces' => $pieces,
            'piecesMap' => $piecesMap,
        ]);
        
        $location = $locationMove['row'].$locationMove['col'];
        
        $pieceDie = array_search($location, $piecesMap);
        
        if ($pieceDie != '') {
            $piecesMap[$pieceDie] = '99';
            $pieces[$piecesNames[$pieceDie]]['location'] = [
                'row' => 9,
                'col' => 9,
            ];
        }
        
        $pieces[$pieceMove]['location'] = $locationMove;
        $piecesMap[$pieces[$pieceMove]['map']] = $location;
        
        $request->session()->put('gameBoard', $pieces);
        $request->session()->put('piecesmap', $piecesMap);
        $request->session()->put('piecesnames', $piecesNames);
        
        $boardata['pieces'] = $pieces;
        $boardata['piecesMap'] = $piecesMap;
        
        $game->board_data = json_encode($boardata);
        $game->moves = $game->moves + 1;
        $game->update();
        
        if ($game->moves == 16) {
            return response()->json([
                'status' => 0,
                'validate' => 'Es un empate',
            ]);
        }
        
        return response()->json([
            'status' => 1,
            'validate' => $checkMove,
            'board_data' => $boardata,
        ]);
    }
    
    /**
     * Change status of game
     * Update scores of users
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function endingGame(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '2')->first();
        
        if ($game == null) {
            return view('errors.404');
        }
        
        $game->status = '2';
        $userLoser = $game->user2;
        
        if ($request->user()->id == $game->user2) {
            $userLoser = $game->user1;
        }
        
        $resultScores = $this->calculateScore($request->user()->id, $userLoser, false);
        
        $game->winner = $request->user()->id;
        $game->loser = $userLoser;
        $game->score_winner = $resultScores[0];
        $game->score_loser = $resultScores[1];
        
        $game->update();
        
        $userBegin = $game->user1;
        
        if ($game->user1 == $game->who_begin) {
            $userBegin = $game->user2;
        }
        
        $this->createPreGame([
            'user1' => $game->user1,
            'user2' => $game->user2,
            'distribution' => $game->distribution,
            'channel' => $game->channel_ably,
            'user_begin' => $userBegin,
        ]);
        
        return response()->json([
            'status' => 1,
            'message' => 'Game end',
        ]);
    }
    
    public function endingGameMachine(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '1')->first();
        
        if ($game == null) {
            return view('errors.404');
        }
        
        $game->status = '2';
        $winUser = false;
        
        // falta revisar && $request->session()->get('truewingame')
        if ($request->input('user') == true) {
            $winUser = true;
            $request->session()->forget('truewingame');
        }
        
        $userScore = User::where('id', $game->user2)->first();
        $scoreWin = $userScore->rating;
        $kWinner = 20;
        $calcNewScoreWin = 0;
        
        if ($scoreWin > 2000) {
            $kWinner = 10;
        }
        
        if ($winUser) {
            $calcNewScoreWin = $kWinner;
            $game->loser = 0;
            $game->winner = $request->user()->id;
            $game->score_winner = $kWinner;
            $userScore->game_win = $userScore->game_win + 1;
        } else {
            $calcNewScoreWin = -$kWinner;
            $game->loser = $request->user()->id;
            $game->winner = 0;
            $game->score_loser = -$kWinner;
            $userScore->game_lose = $userScore->game_lose + 1;
        }
        
        $userScore->rating = intval($scoreWin + $calcNewScoreWin);
        $userScore->game_count = $userScore->game_count + 1;
        
        $userScore->update();
        $game->update();
        
        $request->session()->flash('pregameMachine', true);
        
        return response()->json([
            'status' => 1,
            'message' => 'Game end',
        ]);
    }
    
    public function surrender(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '2')->first();
        
        $game->status = '2';
        $userWinner = $game->user2;
        
        if ($request->user()->id == $game->user2) {
            $userWinner = $game->user1;
        }
        
        $resultScores = $this->calculateScore($userWinner, $request->user()->id, false);
        
        $game->winner = $userWinner;
        $game->loser = $request->user()->id;
        $game->score_winner = $resultScores[0];
        $game->score_loser = $resultScores[1];
        $game->comments = 'El usuario '.$request->user()->name.'('.$request->user()->id.') se ha rendido';
        
        $game->update();
        
        $userBegin = $game->user1;
        
        if ($game->user1 == $game->who_begin) {
            $userBegin = $game->user2;
        }
        
        $this->createPreGame([
            'user1' => $game->user1,
            'user2' => $game->user2,
            'distribution' => $game->distribution,
            'channel' => $game->channel_ably,
            'user_begin' => $userBegin,
        ]);
        
        return response()->json([
            'status' => 1,
            'message' => 'Game end',
        ]);
    }
    
    public function surrenderMachine(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '1')->first();
        
        if ($game == null) {
            return view('errors.404');
        }
        
        $game->status = '2';
        
        $userScore = User::where('id', $game->user2)->first();
        $scoreWin = $userScore->rating;
        $kWinner = -20;
        
        if ($scoreWin > 2000) {
            $kWinner = -10;
        }
        
        $game->loser = $request->user()->id;
        $game->score_loser = $kWinner;
        $game->comments = 'El usuario '.$request->user()->name.'('.$request->user()->id.') se ha rendido';
        
        $userScore->rating = intval($scoreWin + $kWinner);
        $userScore->game_lose = $userScore->game_lose + 1;
        $userScore->game_count = $userScore->game_count + 1;
        
        $userScore->update();
        $game->update();
        
        $request->session()->flash('pregameMachine', true);
        
        return response()->json([
            'status' => 1,
            'message' => 'Game end',
        ]);
    }
    
    public function empateGame(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '2')->first();
        
        $game->status = '2';
        $game->comments = 'Ha sido un empate';
        $userLoser = $game->user2;
        
        if ($request->user()->id == $game->user2) {
            $userLoser = $game->user1;
        }
        
        $resultScores = $this->calculateScore($request->user()->id, $userLoser, true);
        
        $game->winner = $request->user()->id;
        $game->loser = $userLoser;
        $game->score_winner = $resultScores[0];
        $game->score_loser = $resultScores[1];
        
        $game->update();
        
        $userBegin = $game->user1;
        
        if ($game->user1 == $game->who_begin) {
            $userBegin = $game->user2;
        }
        
        $this->createPreGame([
            'user1' => $game->user1,
            'user2' => $game->user2,
            'distribution' => $game->distribution,
            'channel' => $game->channel_ably,
            'user_begin' => $userBegin,
        ]);
        
        return response()->json([
            'status' => 1,
            'message' => 'Game end',
        ]);
    }
    
    public function empateGameMachine(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '1')->first();
        
        $game->status = '2';
        $game->comments = 'Ha sido un empate';
        
        $userScore = User::where('id', $game->user2)->first();
        $scoreWin = $userScore->rating;
        $kWinner = 10;
        
        if ($scoreWin > 2000) {
            $kWinner = 5;
        }
        
        $game->winner = $request->user()->id;
        $game->score_winner = $kWinner;
        
        $userScore->rating = intval($scoreWin + $kWinner);
        $userScore->game_empates = $userScore->game_empates + 1;
        $userScore->game_count = $userScore->game_count + 1;
        
        $userScore->update();
        $game->update();
        
        $request->session()->flash('pregameMachine', true);
        
        return response()->json([
            'status' => 1,
            'message' => 'Game end',
        ]);
    }
    
    public function winByLeave(Request $request)
    {
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '2');
        
        if ($game == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Error no se encuentra',
            ]);
        }
        
        $game = $game->first();
        
        // verificar que sea verdad
        $presenceInChannel = Ably::channel($game->channel_ably)->presence->get();
        
        if (count($presenceInChannel->items) == 1) {
            // calcular puntaje y terminar partida
            $game->status = '2';
            $userLoser = $game->user2;
            
            if ($request->user()->id == $game->user2) {
                $userLoser = $game->user1;
            }
            
            $resultScores = $this->calculateScore($request->user()->id, $userLoser, false);
            
            $game->winner = $request->user()->id;
            $game->loser = $userLoser;
            $game->score_winner = $resultScores[0];
            $game->score_loser = $resultScores[1];
            $game->comments = 'El usuario abandono';
            
            $game->update();
            
            return response()->json([
                'status' => 1,
                'players' => 1,
                'items' => $presenceInChannel,
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Eres un troll',
            ]);
        }
    }
    
    protected function createPreGame($data)
    {
        PreGame::create([
            'user1' => $data['user1'],
            'user2' => $data['user2'],
            'status' => 0,
            'distribution' => $data['distribution'],
            'channel' => $data['channel'],
            'user_begin' => $data['user_begin'],
        ]);
        
        return true;
    }
    
    public function playAgain(Request $request)
    {
        //check status
        $preGame = PreGame::where('user1', $request->user()->id)->orWhere('user2', $request->user()->id);
        if ($preGame == null) {
            return response()->json([
                'status' => 400,
                'message' => 'Error no se encuentra',
            ]);
        }
        
        $preGame = $preGame->first();
        
        // rechazar partida anunciar al otro usuario
        if ($request->filled('noagain')) {
            Ably::channel($preGame->channel)->publish('playagain', [
                'status' => 400,
                'message' => 'El otro jugador no desea jugar',
                'clientId' => encrypt($request->user()->email),
            ]);
            $preGame->delete();
            
            return response()->json([
                'status' => 200,
                'message' => 'Not play again',
            ]);
        }
        
        
        if ($preGame->status == 1 && ! $request->session()->has('yetresponse')) {
            // crear partida y notificar a ambos usuarios
            Ably::channel($preGame->channel)->publish('playagain', ['status' => 200, 'message' => 'Jugar otra vez',]);
            
            $distribution = $this->getDistribution($preGame->distribution);
            $gameboard = $this->buildPieces($distribution);
            
            Game::create([
                'user1' => $preGame->user1,
                'user2' => $preGame->user2,
                'status' => '1',
                'distribution' => $distribution,
                'who_begin' => $preGame->user_begin,
                'channel_ably' => $preGame->channel,
                'board_data' => json_encode($gameboard),
            ]);
            
            $preGame->delete();
        } elseif ($preGame->status == 0) {
            $preGame->status = $preGame->status + 1;
            $preGame->update();
            $request->session()->put('yetresponse', true);
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'wait player',
        ]);
    }
    
    public function getGame(Request $request)
    {
        $request->session()->forget('yetresponse');
        HallsWait::where('user', $request->user()->id)->delete();
        
        // obtener partida
        $game = Game::where(function ($query) use ($request) {
            $query->where('user1', $request->user()->id)
                ->orWhere('user2', $request->user()->id);
        })->where('status', '1')->where('type', '2')->first();
        
        $gameboard = $this->buildPieces($game->distribution);
        $request->session()->put('gameBoard', $gameboard['pieces']);
        $request->session()->put('piecesmap', $gameboard['piecesMap']);
        $request->session()->put('piecesnames', $gameboard['piecesNames']);
        $request->session()->put('idgame', $game->id);
        
        $meBegin = ($game->who_begin == $request->user()->id) ? true : false;
        
        return response()->json([
            'distribution' => $game->distribution,
            'me_begin' => $meBegin,
            'status' => 200,
            'channel' => $game->channel_ably,
            'time' => $game->created_at,
            'board_data' => json_encode($gameboard['pieces']),
        ]);
    }
    
    public function createGameChallenge(Request $request)
    {
        $userRetador = User::where('email', decrypt($request->input('user')))->first()->id;
        
        $nChannelGame = Str::random(10);
        $distribution = $this->getDistribution();
        
        $whoBegin = [$userRetador, $request->user()->id];
        shuffle($whoBegin);
        
        $gameboard = $this->buildPieces($distribution);
        $request->session()->put('gameBoard', $gameboard['pieces']);
        $request->session()->put('piecesmap', $gameboard['piecesMap']);
        $request->session()->put('piecesnames', $gameboard['piecesNames']);
        
        $request->session()->put('pregamereto', true);
        
        Game::create([
            'user1' => $userRetador,
            'user2' => $request->user()->id,
            'status' => '1',
            'distribution' => $distribution,
            'who_begin' => $whoBegin[0],
            'channel_ably' => $nChannelGame,
            'board_data' => json_encode($gameboard),
            // 'channel_ably' => $hall_user->channel,
        ]);
        
        return response()->json([
            'status' => 200,
            'message' => 'Game create',
        ]);
    }
    
    /**
     * $user1 => id of user
     * $user2 => id of user
     * $win   => which player win
     */
    protected function calculateScore($userWin, $userLoser, $empate = false)
    {
        $userWin = User::where('id', $userWin)->first();
        $userLoser = User::where('id', $userLoser)->first();
        
        $scoreWin = $userWin->rating;
        $scoreLoser = $userLoser->rating;
        $diferencia = 0;
        $winMayor = true;
        
        if ($scoreWin > $scoreLoser) {
            $diferencia = $scoreWin - $scoreLoser;
        } elseif ($scoreWin < $scoreLoser) {
            $diferencia = $scoreWin - $scoreWin;
            $winMayor = false;
        }
        
        $diferenciaFavor = 0.5;
        $diferenciaContra = 0.5;
        
        if ($diferencia > 4) {
            $rango = Scores::where('value_min', '<=', $diferencia)->where('value_max', '>=', $diferencia)->first();
            $diferenciaFavor = $rango->favor / 100;
            $diferenciaContra = $rango->contra / 100;
        }
        
        $kWinner = 200;
        $kLoser = 200;
        $wWinner = 1;
        $wLoser = 0;
        
        if ($scoreWin > 2000) {
            $kWinner = 100;
        }
        
        if ($scoreLoser > 2000) {
            $kLoser = 100;
        }
        
        if ($empate) {
            $wWinner = 0.5;
            $wLoser = 0.5;
        }
        
        /**
         * R1 = R0 + K(W-P)
         * K = 20 if R0 > 2000 K = 10
         *
         * R1 => new score
         */
        $calcNewScoreWin = 0;
        $calcNewScoreLoser = 0;
        
        if ($winMayor) {
            $calcNewScoreWin = ($kWinner * ($wWinner - $diferenciaFavor));
            $calcNewScoreLoser = ($kLoser * ($wLoser - $diferenciaContra));
        } else {
            $calcNewScoreWin = ($kWinner * ($wWinner - $diferenciaContra));
            $calcNewScoreLoser = ($kLoser * ($wLoser - $diferenciaFavor));
        }
        
        if ($empate) {
            $userWin->game_empates = $userWin->game_empates + 1;
            $userLoser->game_empates = $userLoser->game_empates + 1;
        } else {
            $userWin->game_win = $userWin->game_win + 1;
            $userLoser->game_lose = $userLoser->game_lose + 1;
        }
        
        $userWin->rating = intval($scoreWin + $calcNewScoreWin);
        $userWin->game_count = $userWin->game_count + 1;
        $userWin->update();
        
        $userLoser->rating = intval($scoreLoser + $calcNewScoreLoser);
        $userLoser->game_count = $userLoser->game_count + 1;
        $userLoser->update();
        
        return [intval($calcNewScoreWin), intval($calcNewScoreLoser)];
    }
    
    public function truncateGame()
    {
        // select * from scores where value_min <= 163 and value_max >= 163
        DB::statement('truncate table partidas; truncate table halls_wait');
        
        return response()->json([
            'status' => ':)',
        ]);
    }
    
    public function getAblyMoves(Request $request)
    {
        return response()->json([
            'movesHorse_35' => $this->getMovesHorse([
                'row' => 3,
                'col' => 5,
            ], $request->session()->get('piecesmap')),
            'movesHorse_00' => $this->getMovesHorse([
                'row' => 0,
                'col' => 0,
            ]),
            'movesHorse_51' => $this->getMovesHorse([
                'row' => 5,
                'col' => 1,
            ]),
            'movesAlfil_32' => $this->getMovesAlfil([
                'row' => 3,
                'col' => 2,
            ], $request->session()->get('piecesmap')),
            'movesAlfil_00' => $this->getMovesAlfil([
                'row' => 0,
                'col' => 0,
            ]),
            'movesAlfil_11' => $this->getMovesAlfil([
                'row' => 1,
                'col' => 1,
            ]),
            'movesTower_24' => $this->getMovesTower([
                'row' => 2,
                'col' => 4,
            ], $request->session()->get('piecesmap')),
            'movesTower_30' => $this->getMovesTower([
                'row' => 3,
                'col' => 0,
            ]),
            'movesTower_43' => $this->getMovesTower([
                'row' => 4,
                'col' => 3,
            ]),
            'pieces' => $this->buildPieces(),
        ]);
    }
    
    public function playMachineIndex()
    {
        return view('play-machine-index');
    }
    
    public function playWithoutAuth(Request $request)
    {
        $distributionLast = '';
        $whoBegin = [-1, 0];
        
        shuffle($whoBegin);
        
        $aDistributions = [
            0 => '28,37,23,19,30,5,27',
            1 => '1,29,21,28,36,4,25',
            2 => '28,12,38,54,21,20,30',
            3 => '28,21,27,30,12,38,54',
            4 => '28,12,38,54,21,27,30',
            5 => '3,31,47,15,9,41,25',
            6 => '64,2,50,34,31,47,15',
            7 => '9,48,42,2,30,46,14',
            8 => '9,54,35,2,24,10,29',
            9 => '25,9,53,12,5,17,2',
            10 => '25,14,15,17,19,26,28',
            11 => '27,16,3,43,34,19,18',
            12 => '27,12,38,54,21,20,30',
            13 => '37,48,36,7,18,11,28',
            14 => '37,32,29,62,4,38,28',
        ];
        
        $aDistributions = $this->distributionsWithoutAuth();
        
        shuffle($aDistributions);
        $distribution = $aDistributions[0];
        $gameboard = $this->buildPieces($distribution);
        
        $game = Game::create([
            'user1' => 0,
            'user2' => -1,
            'status' => '1',
            'distribution' => $distribution,
            'who_begin' => -1,
            'board_data' => json_encode($gameboard),
            'type' => 3,
            'comments' => 'Session user => '.$request->session()->getId().', ',
        ]);
        
        $request->session()->put('gameBoard', $gameboard['pieces']);
        $request->session()->put('piecesmap', $gameboard['piecesMap']);
        $request->session()->put('piecesnames', $gameboard['piecesNames']);
        $request->session()->put('gameBoardTime', $game->created_at);
        $request->session()->put('idgame', $game->id);
        $request->session()->forget('pregamereto');
        
        // $meBegin = ($whoBegin[0] == -1) ? true : false;
        $meBegin = true;
        
        if ( ! $request->session()->has('eloitchess')) {
            $request->session()->put('eloitchess', 10000);
        } elseif ($request->session()->get('eloitchess') < 0) {
            $request->session()->put('eloitchess', 10000);
        }
        
        return response()->json([
            'preGame' => false,
            'distribution' => $distribution,
            'begin' => $meBegin,
            'time_game' => $game->created_at,
        ]);
    }
    
    public function endingGameMachineScreen(Request $request)
    {
        $game = Game::select('id', 'moves', 'board_data', 'who_begin', 'status', 'comments')->where('id',
            $request->session()->get('idgame'))->first();
        
        if ($game != null) {
            $scoreCurrent = $request->session()->get('eloitchess');
            
            $game->status = 2;
            
            $userInput = $request->input('user');
            if ($userInput == true) {
                $game->loser = 0;
                $game->winner = -1;
                $game->comments = $game->comments." ganó al servidor";
                $scoreCurrent = $scoreCurrent + (200 * 0.6);
            } elseif ($userInput == 2) {
                $game->loser = 0;
                $game->winner = -1;
                $game->comments = $game->comments." fué empate";
                $scoreCurrent = $scoreCurrent + (200 * 0.1);
            } else {
                $game->loser = -1;
                $game->winner = 0;
                $game->comments = $game->comments." ganó el servidor";
                $scoreCurrent = $scoreCurrent + (200 * -0.4);
            }
            
            $game->update();
            
            $request->session()->put('eloitchess', $scoreCurrent);
            
            return response()->json([
                'status' => 200,
                'message' => 'Game ending',
                'new_eloit' => $scoreCurrent,
            ]);
        }
        
        return view('errors.404');
    }
    
    public function distributionsWithoutAuth()
    {
        return [
            0 => '28,37,23,19,30,5,27',
            1 => '1,29,21,28,36,4,25',
            2 => '28,12,38,54,21,20,30',
            3 => '28,21,27,30,12,38,54',
            4 => '28,12,38,54,21,27,30',
            5 => '3,31,47,15,9,41,25',
            6 => '64,2,50,34,31,47,15',
            7 => '9,48,42,2,30,46,14',
            8 => '9,54,35,2,24,10,29',
            9 => '25,9,53,12,5,17,2',
            10 => '25,14,15,17,19,26,28',
            11 => '27,16,3,43,34,19,18',
            12 => '27,12,38,54,21,20,30',
            13 => '37,48,36,7,18,11,28',
            14 => '37,32,29,62,4,38,28',
            15 => '1,10,18,64,48,38,57',
            16 => '1,39,29,21,23,18,28',
            17 => '1,63,58,13,54,9,50',
            18 => '1,63,45,29,48,23,19',
            19 => '1,64,29,21,10,9,6',
            20 => '1,64,38,4,55,26,49',
            21 => '1,64,47,35,55,54,22',
            22 => '9,61,53,21,6,14,44',
            23 => '9,62,37,46,55,44,52',
            24 => '9,64,23,17,50,42,10',
            25 => '17,60,54,38,40,13,52',
            26 => '17,61,34,25,51,19,18',
            27 => '17,61,54,15,5,6,46',
            28 => '17,62,41,45,13,40,30',
            29 => '17,62,54,45,55,47,30',
            30 => '17,63,54,5,46,2,43',
            31 => '17,56,2,30,61,43,5',
            32 => '25,5,23,60,61,55,62',
            33 => '25,5,55,21,46,4,45',
            34 => '25,61,12,31,46,14,44',
            35 => '25,54,9,26,61,27,33',
            36 => '25,7,10,63,64,42,6',
            37 => '25,8,56,45,62,50,14',
            38 => '25,32,17,29,1,26,52',
            39 => '33,49,13,52,61,41,58',
            40 => '33,61,64,45,6,3,19',
            41 => '33,6,64,13,63,1,54',
            42 => '33,46,13,19,63,5,53',
            43 => '33,54,55,41,43,34,36',
            44 => '33,54,56,48,63,1,44',
            45 => '33,62,61,29,14,4,47',
            46 => '33,15,62,61,54,2,12',
            47 => '33,63,40,16,48,8,56',
            48 => '33,16,18,40,28,52,54',
            49 => '33,64,50,63,7,9,6',
            50 => '41,21,60,46,63,12,9',
            51 => '41,29,12,55,6,33,23',
            52 => '41,61,49,33,16,51,42',
            53 => '41,6,38,36,62,24,21',
            54 => '41,62,27,34,14,54,19',
            55 => '41,15,6,45,8,24,11',
            56 => '41,23,28,10,4,30,55',
            57 => '41,47,1,28,48,44,63',
            58 => '41,63,51,6,16,3,46',
            59 => '41,63,23,42,13,9,29',
            60 => '41,8,6,11,15,24,18',
            61 => '41,56,14,53,63,3,38',
            62 => '41,64,51,36,8,19,23',
            63 => '41,64,56,38,8,2,28',
            64 => '49,17,50,26,48,41,2',
            65 => '49,19,20,17,22,18,50',
            66 => '49,19,31,17,23,3,60',
            67 => '49,4,46,38,32,14,10',
            68 => '49,60,10,53,4,3,19',
            69 => '49,5,10,21,14,56,6',
            70 => '49,29,61,35,22,18,27',
            71 => '49,6,4,53,62,8,17',
            72 => '49,14,27,58,48,50,37',
            73 => '49,22,21,27,42,2,52',
            74 => '49,30,3,28,21,22,11',
            75 => '49,30,6,27,24,3,42',
            76 => '49,30,46,22,20,3,28',
            77 => '49,54,53,29,39,1,44',
            78 => '49,62,1,60,4,22,26',
            79 => '49,31,64,21,23,14,11',
            80 => '49,39,46,61,3,10,14',
            81 => '49,16,4,19,17,25,33',
            82 => '49,24,18,58,38,22,54',
            83 => '49,40,22,38,39,3,21',
            84 => '49,64,12,35,11,20,28',
            85 => '57,27,62,43,21,25,36',
            86 => '57,43,41,44,51,59,52',
            87 => '57,4,9,13,5,63,28',
            88 => '57,12,58,39,53,42,36',
            89 => '57,28,36,43,12,20,50',
            90 => '57,5,7,11,54,56,55',
            91 => '57,21,3,10,26,62,33',
            92 => '57,37,45,36,28,60,33',
            93 => '57,37,45,36,28,60,43',
            94 => '57,22,3,61,15,23,19',
            95 => '57,15,4,29,7,3,22',
            96 => '57,15,8,28,6,27,50',
            97 => '2,43,41,63,46,48,58',
            98 => '2,38,63,45,60,55,21',
            99 => '2,39,50,53,63,10,14',
            100 => '10,57,61,48,31,63,46',
            101 => '10,52,18,22,62,58,35',
            102 => '10,60,40,62,48,38,56',
            103 => '10,54,11,43,64,49,35',
            104 => '10,62,63,45,47,56,32',
            105 => '10,63,26,29,62,18,46',
            106 => '10,8,2,47,64,11,59',
            107 => '10,16,9,26,57,11,28',
            108 => '10,64,48,46,32,62,29',
            109 => '18,31,29,44,12,26,2',
            110 => '18,47,46,14,54,55,31',
            111 => '18,63,6,29,35,49,46',
            112 => '18,32,29,34,12,10,9',
            113 => '18,56,44,21,61,57,38',
            114 => '18,64,26,50,57,34,12',
            115 => '18,64,46,54,55,37,39',
            116 => '18,64,8,58,57,26,52',
            117 => '26,54,64,45,63,4,14',
            118 => '26,23,22,14,32,27,37',
            119 => '26,8,2,53,63,59,14',
            120 => '34,63,3,55,56,5,48',
            121 => '34,63,48,22,62,17,2',
            122 => '34,64,37,55,8,26,15',
            123 => '42,12,11,54,24,13,20',
            124 => '42,7,11,23,15,39,6',
            125 => '42,32,34,46,5,62,20',
            126 => '42,56,46,55,6,11,14',
            127 => '42,64,34,14,7,29,55',
            128 => '50,26,51,29,54,42,12',
            129 => '50,28,51,36,56,12,25',
            130 => '50,28,29,4,21,20,36',
            131 => '50,5,10,18,38,47,41',
            132 => '50,29,11,43,22,13,26',
            133 => '50,29,23,39,32,26,19',
            134 => '50,14,23,11,62,17,19',
            135 => '50,22,42,3,26,2,39',
            136 => '50,22,7,23,13,16,14',
            137 => '50,22,24,39,31,15,5',
            138 => '50,62,53,47,15,3,20',
            139 => '50,62,54,29,22,26,15',
            140 => '50,7,22,43,6,15,36',
            141 => '50,8,6,2,1,3,7',
            142 => '50,24,12,27,4,16,51',
            143 => '50,32,17,39,13,46,20',
            144 => '50,56,46,55,6,26,14',
            145 => '50,56,54,35,18,34,46',
            146 => '58,2,31,59,64,29,50',
            147 => '58,34,2,50,18,12,27',
            148 => '58,20,31,34,13,4,55',
            149 => '58,21,12,45,23,20,27',
            150 => '58,37,12,47,22,30,6',
            151 => '58,14,38,26,17,25,31',
            152 => '58,30,12,55,23,14,27',
            153 => '58,30,55,13,37,3,47',
            154 => '58,7,26,29,2,55,62',
            155 => '58,31,6,45,13,11,28',
            156 => '58,8,64,19,48,3,22',
            157 => '3,53,40,11,60,47,19',
            158 => '3,31,47,37,63,56,22',
            159 => '3,64,46,40,55,49,27',
            160 => '11,50,43,37,57,34,19',
            161 => '11,54,62,27,60,58,19',
            162 => '11,63,57,40,48,12,54',
            163 => '11,16,19,58,59,12,60',
            164 => '11,32,17,2,49,19,12',
            165 => '11,56,27,10,57,8,19',
            166 => '11,64,57,46,63,28,55',
            167 => '11,64,19,63,62,3,6',
            168 => '19,10,11,20,28,27,18',
            169 => '19,31,29,37,12,19,2',
            170 => '19,63,57,10,8,9,28',
            171 => '19,63,58,12,27,16,51',
            172 => '19,63,11,50,56,18,15',
            173 => '19,63,11,53,56,18,50',
            174 => '19,56,61,27,59,6,11',
            175 => '19,64,11,63,53,27,38',
            176 => '27,57,12,5,8,42,55',
            177 => '27,13,38,30,46,47,28',
            178 => '27,61,62,6,7,14,53',
            179 => '27,14,53,30,63,42,28',
            180 => '27,16,52,30,53,25,28',
            181 => '27,16,52,46,53,25,30',
            182 => '27,40,56,36,8,18,16',
            183 => '35,57,43,15,8,3,32',
            184 => '35,59,34,19,3,48,51',
            185 => '35,52,30,14,45,44,38',
            186 => '35,62,5,11,14,39,46',
            187 => '35,31,18,19,54,43,44',
            188 => '35,55,43,26,22,59,51',
            189 => '35,32,59,19,53,43,31',
            190 => '35,56,59,19,26,43,42',
            191 => '43,20,7,38,21,5,53',
            192 => '43,5,35,30,15,63,12',
            193 => '43,53,38,11,25,59,41',
            194 => '43,6,1,30,15,3,21',
            195 => '43,6,4,14,3,5,11',
            196 => '43,6,47,12,15,2,30',
            197 => '43,14,21,22,7,13,9',
            198 => '43,22,12,55,5,15,35',
            199 => '43,7,39,13,14,3,22',
            200 => '43,15,28,38,32,55,5',
            201 => '43,8,42,22,7,51,23',
            202 => '43,8,16,11,24,17,41',
            203 => '43,56,51,22,1,2,14',
            204 => '43,56,55,30,62,4,12',
            205 => '51,57,40,5,64,25,23',
            206 => '51,3,5,14,6,4,11',
            207 => '51,27,43,20,35,19,10',
            208 => '51,5,34,22,14,55,13',
            209 => '51,5,35,26,18,43,29',
            210 => '51,5,52,43,4,20,12',
            211 => '51,5,61,19,13,38,22',
            212 => '51,13,43,37,31,52,26',
            213 => '51,6,3,23,11,19,35',
            214 => '51,14,50,28,15,47,21',
            215 => '51,22,18,15,32,38,12',
            216 => '51,30,63,13,7,43,20',
            217 => '51,7,10,23,15,34,6',
            218 => '51,7,64,29,22,1,13',
            219 => '51,23,43,55,9,52,19',
            220 => '51,23,21,20,37,39,13',
            221 => '51,23,22,11,4,48,29',
            222 => '51,31,18,38,5,22,20',
            223 => '51,31,43,18,20,64,14',
            224 => '51,16,36,32,24,4,15',
            225 => '51,40,24,20,14,5,46',
            226 => '59,9,2,36,21,13,4',
            227 => '59,3,29,12,5,11,27',
            228 => '59,31,22,30,14,3,11',
            229 => '59,39,23,55,49,17,33',
            230 => '4,33,42,27,64,8,31',
            231 => '4,49,34,36,48,55,28',
            232 => '4,57,41,44,55,64,37',
            233 => '12,50,20,25,42,58,44',
            234 => '12,52,59,53,36,11,28',
            235 => '12,52,55,19,51,58,44',
            236 => '12,45,53,43,59,60,37',
            237 => '12,38,62,44,47,55,14',
            238 => '20,63,33,39,54,23,48',
            239 => '20,56,55,15,57,8,53',
            240 => '28,1,61,55,57,36,15',
            241 => '28,9,61,8,25,57,26',
            242 => '28,9,56,49,24,34,7',
            243 => '28,49,2,50,48,8,56',
            244 => '28,49,64,15,9,8,55',
            245 => '28,50,47,5,55,29,15',
            246 => '28,37,1,36,19,64,20',
            247 => '28,14,20,16,54,34,37',
            248 => '28,14,36,19,53,20,35',
            249 => '28,46,55,7,37,64,49',
            250 => '28,47,27,50,33,29,64',
            251 => '28,55,13,10,15,43,50',
            252 => '28,55,63,54,48,13,60',
            253 => '36,1,51,64,8,28,56',
            254 => '36,57,58,14,8,49,15',
            255 => '36,26,34,29,27,18,4',
            256 => '36,42,11,39,43,22,35',
            257 => '36,50,11,33,10,34,35',
            258 => '36,43,37,44,29,35,28',
            259 => '36,43,38,34,21,54,29',
            260 => '36,51,47,52,30,53,25',
            261 => '36,12,58,14,53,42,28',
            262 => '36,20,4,28,26,50,38',
            263 => '36,52,30,14,45,35,38',
            264 => '36,52,30,14,45,44,38',
            265 => '36,29,52,43,38,61,20',
            266 => '36,29,47,43,30,35,52',
            267 => '36,29,47,43,38,61,35',
            268 => '36,45,2,11,62,61,3',
            269 => '36,45,35,38,52,30,14',
            270 => '36,53,34,63,51,44,31',
            271 => '36,61,42,47,15,10,3',
            272 => '36,61,32,15,5,49,63',
            273 => '36,6,37,13,64,2,63',
            274 => '36,46,35,38,60,30,24',
            275 => '36,62,44,1,22,55,12',
            276 => '36,7,1,43,62,22,29',
            277 => '36,7,33,6,60,49,44',
            278 => '36,7,51,64,9,28,14',
            279 => '36,7,64,13,8,14,63',
            280 => '36,15,8,32,23,16,14',
            281 => '36,23,30,14,24,21,15',
            282 => '36,23,55,4,50,58,2',
            283 => '36,31,6,28,55,24,11',
            284 => '36,47,10,11,56,59,5',
            285 => '36,56,19,14,9,4,62',
            286 => '36,64,19,63,32,31,4',
            287 => '36,64,44,43,32,10,29',
            288 => '44,17,13,4,40,10,3',
            289 => '44,17,21,14,15,36,4',
            290 => '44,3,33,15,5,12,18',
            291 => '44,5,49,64,14,1,11',
            292 => '44,5,54,27,35,9,21',
            293 => '44,6,27,22,3,45,11',
            294 => '44,14,63,22,6,50,5',
            295 => '44,16,8,14,1,2,4',
            296 => '44,24,10,12,64,14,29',
            297 => '44,56,55,26,3,11,4',
            298 => '52,17,46,23,24,51,18',
            299 => '52,25,3,38,22,14,6',
            300 => '52,2,4,26,10,62,18',
            301 => '52,5,18,22,14,26,13',
            302 => '52,5,19,36,12,15,28',
            303 => '52,5,30,22,3,10,39',
            304 => '52,13,25,56,39,15,3',
            305 => '52,21,5,29,13,12,22',
            306 => '52,6,7,8,62,17,19',
            307 => '52,6,23,22,62,17,19',
            308 => '52,22,51,11,25,5,29',
            309 => '52,30,46,19,23,26,13',
            310 => '52,7,4,31,24,44,26',
            311 => '52,15,10,44,24,53,20',
            312 => '52,64,3,19,33,4,29',
            313 => '52,64,28,34,57,44,39',
            314 => '60,6,33,46,15,41,23',
            315 => '60,14,44,20,23,52,27',
            316 => '60,30,7,38,21,29,25',
            317 => '60,55,38,37,39,35,21',
            318 => '60,64,27,20,5,61,39',
            319 => '5,41,26,53,48,55,27',
            320 => '5,49,27,25,64,22,45',
            321 => '5,57,25,51,56,62,38',
            322 => '13,55,12,50,63,14,44',
            323 => '13,63,50,34,55,41,43',
            324 => '13,64,33,25,38,56,50',
            325 => '13,64,55,56,43,10,50',
            326 => '21,25,49,28,41,17,12',
            327 => '21,58,55,2,51,33,44',
            328 => '21,43,51,35,50,53,28',
            329 => '21,59,29,10,50,27,46',
            330 => '21,59,62,43,42,2,37',
            331 => '21,46,57,44,25,53,50',
            332 => '21,62,61,28,60,54,18',
            333 => '21,7,17,22,8,60,13',
            334 => '21,15,8,35,50,57,30',
            335 => '21,55,29,48,58,45,1',
            336 => '21,8,33,47,64,60,53',
            337 => '21,56,18,59,63,11,50',
            338 => '29,41,12,10,62,53,56',
            339 => '29,57,2,38,8,56,20',
            340 => '29,57,26,4,1,14,60',
            341 => '29,57,60,63,7,18,58',
            342 => '29,2,46,42,57,23,17',
            343 => '29,18,56,11,64,21,35',
            344 => '29,50,41,28,48,9,36',
            345 => '29,50,42,2,41,51,4',
            346 => '29,50,37,49,41,28,42',
            347 => '29,50,37,10,63,28,8',
            348 => '29,3,5,50,62,17,8',
            349 => '29,43,7,50,8,57,36',
            350 => '29,20,56,5,38,2,53',
            351 => '29,54,28,15,52,30,11',
            352 => '29,62,12,50,14,46,9',
            353 => '29,62,37,18,6,21,50',
            354 => '29,62,30,36,40,37,13',
            355 => '29,7,39,63,58,26,2',
            356 => '29,55,26,22,36,28,45',
            357 => '29,55,30,34,51,28,40',
            358 => '29,24,28,63,42,51,36',
            359 => '29,40,37,6,60,30,36',
            360 => '29,56,28,7,57,31,45',
            361 => '29,56,37,4,22,12,36',
            362 => '29,64,34,2,1,18,8',
            363 => '37,9,56,5,2,17,3',
            364 => '37,17,50,38,8,62,57',
            365 => '37,33,11,36,34,15,64',
            366 => '37,49,57,4,1,59,2',
            367 => '37,49,43,64,7,20,10',
            368 => '37,57,8,11,58,7,49',
            369 => '37,2,49,55,10,56,19',
            370 => '37,2,36,38,63,29,45',
            371 => '37,18,20,30,15,22,28',
            372 => '37,50,20,28,59,11,30',
            373 => '37,3,35,18,49,7,4',
            374 => '37,21,36,35,33,29,19',
            375 => '37,6,27,25,4,31,42',
            376 => '37,22,38,36,13,45,29',
            377 => '37,62,17,18,9,12,1',
            378 => '37,7,4,45,63,29,10',
            379 => '37,8,50,17,1,56,57',
            380 => '37,8,50,58,57,43,49',
            381 => '37,16,27,9,62,59,19',
            382 => '37,16,27,15,62,59,11',
            383 => '37,64,42,11,3,46,4',
            384 => '45,1,5,2,4,3,12',
            385 => '45,57,53,18,9,4,40',
            386 => '45,6,32,21,62,26,19',
            387 => '53,1,4,21,15,10,6',
            388 => '53,17,41,23,16,56,26',
            389 => '53,25,17,12,4,5,26',
            390 => '53,25,4,47,15,42,21',
            391 => '53,2,1,27,57,6,7',
            392 => '53,10,52,26,16,31,11',
            393 => '53,18,22,39,24,20,35',
            394 => '53,34,26,35,12,13,21',
            395 => '53,3,57,2,12,13,21',
            396 => '53,12,57,20,6,49,14',
            397 => '53,6,52,26,2,29,30',
            398 => '53,30,38,52,10,11,20',
            399 => '53,7,2,4,10,14,22',
            400 => '53,7,3,21,6,28,24',
            401 => '53,15,10,20,39,49,2',
            402 => '53,39,12,11,26,3,22',
            403 => '53,8,2,41,48,4,25',
            404 => '53,32,2,52,33,5,45',
            405 => '61,12,20,38,30,6,52',
            406 => '61,7,51,36,3,23,6',
            407 => '61,7,15,27,39,51,53',
            408 => '61,8,6,3,2,55,23',
            409 => '6,41,21,44,50,28,27',
            410 => '6,51,26,35,57,44,28',
            411 => '6,59,64,25,51,56,57',
            412 => '6,63,14,37,17,5,30',
            413 => '14,25,62,26,58,17,50',
            414 => '14,33,41,13,59,50,22',
            415 => '14,41,27,28,50,59,52',
            416 => '14,57,64,15,8,63,6',
            417 => '14,50,13,25,21,58,43',
            418 => '14,58,22,18,50,13,46',
            419 => '14,51,58,35,50,59,61',
            420 => '14,51,53,22,58,13,44',
            421 => '14,21,54,42,57,62,17',
            422 => '14,61,13,36,25,22,50',
            423 => '14,8,62,15,64,61,6',
            424 => '14,64,36,57,43,62,22',
            425 => '22,1,62,63,57,7,42',
            426 => '22,49,52,9,63,34,55',
            427 => '22,57,61,29,64,2,21',
            428 => '22,60,10,46,36,18,50',
            429 => '22,8,63,42,1,34,60',
            430 => '30,1,4,14,21,38,46',
            431 => '30,11,64,43,50,20,6',
            432 => '30,37,58,3,59,2,51',
            433 => '38,50,2,4,29,26,58',
            434 => '38,50,55,59,2,21,11',
            435 => '38,7,3,39,31,6,30',
            436 => '46,9,10,26,2,1,21',
            437 => '46,10,7,18,2,49,4',
            438 => '46,18,2,9,10,26,19',
            439 => '46,4,45,19,3,50,21',
            440 => '54,9,2,15,16,6,10',
            441 => '54,2,3,18,10,1,4',
            442 => '54,2,55,30,19,56,32',
            443 => '54,3,25,26,57,2,11',
            444 => '54,3,21,19,13,9,45',
            445 => '54,11,27,45,12,18,36',
            446 => '54,4,3,10,31,33,50',
            447 => '54,28,4,6,21,5,11',
            448 => '54,5,46,53,7,4,55',
            449 => '54,14,46,22,30,38,6',
            450 => '54,7,18,45,24,20,13',
            451 => '54,16,2,27,57,4,22',
            452 => '62,34,11,53,8,5,54',
            453 => '62,39,18,28,7,2,24',
            454 => '7,57,36,47,64,54,26',
            455 => '7,59,22,44,50,47,26',
            456 => '7,59,64,36,52,34,53',
            457 => '7,64,11,45,59,13,35',
            458 => '15,1,60,43,18,58,45',
            459 => '15,49,41,57,10,3,1',
            460 => '15,49,58,34,57,41,51',
            461 => '15,57,42,58,43,50,35',
            462 => '15,2,9,42,57,31,17',
            463 => '15,2,64,10,57,5,50',
            464 => '15,50,49,9,26,2,57',
            465 => '15,58,56,18,49,9,47',
            466 => '15,59,53,35,64,51,57',
            467 => '15,4,5,24,57,49,6',
            468 => '15,52,26,23,50,51,36',
            469 => '15,5,7,24,40,32,6',
            470 => '15,13,11,18,12,14,4',
            471 => '15,54,4,38,37,6,55',
            472 => '15,62,64,43,61,62,60',
            473 => '15,63,1,20,58,2,46',
            474 => '15,56,46,62,64,32,20',
            475 => '15,56,63,58,41,10,46',
            476 => '15,64,9,38,61,62,19',
            477 => '23,1,63,2,10,61,9',
            478 => '23,41,49,44,60,59,42',
            479 => '23,57,33,26,2,25,34',
            480 => '23,57,11,63,9,27,50',
            481 => '23,2,3,43,57,58,18',
            482 => '23,2,62,51,57,5,19',
            483 => '23,34,36,26,2,52,53',
            484 => '23,42,51,30,50,22,35',
            485 => '23,50,41,58,9,2,18',
            486 => '23,50,47,36,55,33,26',
            487 => '23,51,57,34,49,2,61',
            488 => '23,59,15,49,50,24,57',
            489 => '23,64,49,28,8,25,43',
            490 => '31,1,44,13,4,28,23',
            491 => '31,57,6,13,12,8,20',
            492 => '31,2,46,43,33,26,23',
            493 => '31,10,25,11,1,64,9',
            494 => '31,59,41,53,3,17,13',
            495 => '31,59,44,23,3,10,13',
            496 => '39,1,2,47,42,34,30',
            497 => '39,1,63,23,64,2,38',
            498 => '39,49,44,50,2,14,11',
            499 => '39,49,29,2,50,1,20',
            500 => '39,2,59,43,58,4,19',
            501 => '39,3,49,31,4,25,38',
            502 => '39,8,9,40,49,41,31',
            503 => '47,10,45,16,59,8,44',
            504 => '47,50,53,10,9,14,51',
            505 => '47,11,45,14,41,15,44',
            506 => '47,11,45,48,25,8,44',
            507 => '47,20,45,21,49,8,44',
            508 => '55,1,5,13,8,50,30',
            509 => '55,41,54,1,9,7,42',
            510 => '55,49,54,47,6,19,28',
            511 => '55,2,38,39,8,43,19',
            512 => '55,26,50,46,19,24,13',
            513 => '55,3,34,22,6,29,11',
            514 => '55,11,49,17,4,7,35',
            515 => '55,11,43,36,25,29,14',
            516 => '55,59,14,42,11,47,31',
            517 => '55,5,7,37,28,6,29',
            518 => '55,46,19,20,37,4,52',
            519 => '63,7,31,33,49,55,1',
            520 => '8,57,64,46,50,9,5',
            521 => '8,50,37,60,57,35,63',
            522 => '8,58,10,26,39,23,55',
            523 => '8,52,33,38,50,43,27',
            524 => '8,60,64,26,45,34,28',
            525 => '8,62,53,30,18,56,34',
            526 => '16,41,9,46,58,25,20',
            527 => '24,57,12,52,1,28,56',
            528 => '40,2,39,50,15,23,42',
            529 => '56,41,21,58,57,49,42',
            530 => '56,57,30,26,41,12,51',
            531 => '56,10,5,29,19,2,4',
            532 => '56,50,51,36,42,43,29',
            533 => '56,50,51,29,41,42,35',
            534 => '56,50,5,42,41,51,26',
            535 => '56,50,5,51,42,7,43',
            536 => '56,50,8,51,42,6,43',
            537 => '56,3,1,27,10,8,20',
            538 => '56,11,55,58,2,5,42',
            539 => '56,6,3,40,8,51,23',
            540 => '64,9,1,17,10,41,11',
            541 => '64,57,50,30,11,20,60',
            542 => '64,13,29,48,53,5,37',
            543 => '64,21,5,48,53,5,37',
            544 => '64,8,48,43,16,4,56',
        ];
    }
}
