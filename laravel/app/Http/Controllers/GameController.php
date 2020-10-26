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
use Session;
use Ably;

use Illuminate\Support\Facades\Log;

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
        //        dd($request);
        
        //        return response()->json([
        //            $request->session()->getId(),
        //        ]);
        
        $distributionLast = '';
        $whoBegin = [-1, 0];
        
        /*if ($request->session()->has('pregameMachine')) {
            // obtener la ultima parttida
            $gameLast = Game::where('id', $request->session()->get('idgame'))->first();
            
            $distributionLast = $gameLast->distribution;
            
            if ($request->user()->id == $gameLast->who_begin) {
                $whoBegin = [0, $request->user()->id];
            }
            
            $request->session()->forget('pregameMachine');
        } else {
            shuffle($whoBegin);
        }*/
        
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
}
