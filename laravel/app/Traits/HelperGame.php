<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use App\Distribuciones as Distros;

trait HelperGame
{
    public function getMovesHorse($position, $pieces = ["33", "99", "45", "65", "23", "99", "35"])
    {
        if ($position['row'] > 7) {
            return [
                'moves' => [],
                'moves_attack' => [],
            ];
        }
        
        $row = $position['row'];
        $col = $position['col'];
        
        $moves = [];
        $movesAllow = [];
        $piecesAttack = [];
        
        $moves[1] = ($row + 1).($col + 2);
        $moves[2] = ($row + 1).($col - 2);
        $moves[3] = ($row + 2).($col + 1);
        $moves[4] = ($row + 2).($col - 1);
        $moves[5] = ($row - 2).($col + 1);
        $moves[6] = ($row - 2).($col - 1);
        $moves[7] = ($row - 1).($col + 2);
        $moves[8] = ($row - 1).($col - 2);
        
        $count = 1;
        
        foreach ($moves as $move) {
            if (strpos($move, '8') === false && strpos($move, '9') === false && strlen($move) < 3) {
                if (array_search($move, $pieces) !== false) {
                    $piecesAttack[] = $move;
                } else {
                    $movesAllow[$count] = $move;
                }
                
                $count++;
            }
        }
        
        return [
            'moves' => $movesAllow,
            'moves_attack' => $piecesAttack,
        ];
    }
    
    public function getMovesAlfil($position, $pieces = ["33", "99", "45", "65", "23", "99", "35"])
    {
        if ($position['row'] > 7) {
            return [
                'moves' => [],
                'moves_attack' => [],
            ];
        }
        
        $row = $position['row'];
        $col = $position['col'];
        
        $moves = [];
        $movesAllow = [];
        $piecesAttack = [];
        $count = 1;
        
        //arriba derecha
        $incrementador = 1;
        for ($i = $col; $i < 7; $i++) {
            $location = ($row - $incrementador).($col + $incrementador);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        //arriba izquierda
        $incrementador = 1;
        for ($i = $col; $i > 0; $i--) {
            $location = ($row - $incrementador).($col - $incrementador);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        //abajo derecha
        $incrementador = 1;
        for ($i = $col; $i < 7; $i++) {
            if ($row + $incrementador > 7) {
                break;
            }
            
            $location = ($row + $incrementador).($col + $incrementador);
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        //abajo izquierda
        $incrementador = 1;
        for ($i = $col; $i > 0; $i--) {
            if ($row + $incrementador > 7) {
                break;
            }
            
            $location = ($row + $incrementador).($col - $incrementador);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        $count = 1;
        
        foreach ($moves as $move) {
            if (strpos($move, '-') === false) {
                $movesAllow[$count] = $move;
                $count++;
            }
        }
        
        return [
            'moves' => $movesAllow,
            'moves_attack' => $piecesAttack,
        ];
    }
    
    public function getMovesTower($position, $pieces = ["33", "99", "45", "65", "23", "99", "35"])
    {
        if ($position['row'] > 7) {
            return [
                'moves' => [],
                'moves_attack' => [],
            ];
        }
        
        $row = $position['row'];
        $col = $position['col'];
        
        $moves = [];
        $piecesAttack = [];
        $count = 1;
        
        // toward the down
        $incrementador = 1;
        for ($i = $row; $i < 7; $i++) {
            $location = ($row + $incrementador).($col);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        // toward the up
        $incrementador = 1;
        for ($i = $row; $i > 0; $i--) {
            $location = ($row - $incrementador).($col);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        // toward the right
        $incrementador = 1;
        for ($i = $col; $i < 7; $i++) {
            $location = ($row).($col + $incrementador);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        // toward the left
        $incrementador = 1;
        for ($i = $col; $i > 0; $i--) {
            $location = ($row).($col - $incrementador);
            
            if (array_search($location, $pieces) !== false) {
                $piecesAttack[] = $location;
                break;
            }
            
            $moves[$count++] = $location;
            $incrementador++;
        }
        
        return [
            'moves' => $moves,
            'moves_attack' => $piecesAttack,
        ];
    }
    
    /**
     * @param $type
     * @param $position
     * @param $positionBall
     * @param array $pieces
     * @return bool
     */
    public function getAblyJaqueToCapture($type, $position, $positionBall, $pieces = [])
    {
        $moves = [];
        
        switch ($type) {
            case 1:
                {
                    $moves = $this->getMovesTower($position, $pieces);
                    
                    break;
                }
            
            case 2:
                {
                    $moves = $this->getMovesAlfil($position, $pieces);
                    
                    break;
                }
            
            case 3:
                {
                    $moves = $this->getMovesHorse($position, $pieces);
                    
                    break;
                }
        }
        
        if (array_search($positionBall, $moves['moves_attack']) !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @param $type
     * @param $position
     * @param $positionPiece
     * @param array $pieces
     * @return bool
     */
    public function defensePieceAfterMove($type, $position, $positionPiece, $pieces = [])
    {
        return $this->getAblyJaqueToCapture($type, $position, $positionPiece, $pieces);
    }
    
    /**
     * @param array $pieces
     * @param array $locations
     * @return array
     */
    public function getMovesPieces(Array $pieces, Array $locations)
    {
        $movesPieces = [];
        
        foreach ($pieces as $key => $piece) {
            $type = $piece['type'];
            if ($type == 1) {
                $movesPieces[$key] = $this->getMovesTower([
                    'row' => $piece['location']['row'],
                    'col' => $piece['location']['col'],
                ], $locations);
            } elseif ($type == 2) {
                $movesPieces[$key] = $this->getMovesAlfil([
                    'row' => $piece['location']['row'],
                    'col' => $piece['location']['col'],
                ], $locations);
            } elseif ($type == 3) {
                $movesPieces[$key] = $this->getMovesHorse([
                    'row' => $piece['location']['row'],
                    'col' => $piece['location']['col'],
                ], $locations);
            }
        }
        
        return $movesPieces;
    }
    
    /**
     * @param string $color
     * @param array $locations
     * @return array
     */
    public function getMyPieces($color = 'black', Array $locations = [])
    {
        $myPieces = ['piece-tb', 'piece-ab', 'piece-hb'];
        $youPieces = ['piece-tw', 'piece-aw', 'piece-hw'];
        $myPiecesLoc = array_slice($locations, 4, 3);
        $youPiecesLoc = array_slice($locations, 1, 3);
        
        if ($color === 'white') {
            $myPieces = ['piece-tw', 'piece-aw', 'piece-hw'];
            $youPieces = ['piece-tb', 'piece-ab', 'piece-hb'];
            $myPiecesLoc = array_slice($locations, 1, 3);
            $youPiecesLoc = array_slice($locations, 4, 3);
        }
        
        return [
            'myPieces' => $myPieces,
            'youPieces' => $youPieces,
            'myPiecesLoc' => $myPiecesLoc,
            'youPiecesLoc' => $youPiecesLoc,
        ];
    }
    
    /**
     * @param $args
     * @return array
     */
    public function getMoveWhenIsJaquePlayer($args)
    {
        $resBox = '';
        $resPiece = '';
        $resLocation = [];
        $resMessage = '';
        $resAction = 1;
        $resLine = __LINE__;
        
        // lo que recibe por params
        $pieces = $args['pieces'];
        $movesPieces = $args['movesPieces'];
        $youPieceJaque = $args['pieceJaque'];
        $myPieces = $args['piecesMachine'];
        $myAttack = $args['attackMachine'];
        $allMyMoves = $args['allMovesMachine'];
        $piecesMap = $args['locations'];
        $pieceBallLoc = $piecesMap[0];
        
        $pieceJaque = $pieces[$youPieceJaque];
        $pieceLocationOrigin = $pieceJaque['location'];
        $locationPieceJaque = $pieceLocationOrigin['row'].$pieceLocationOrigin['col'];
        $whoAttackYou = $movesPieces[$youPieceJaque]['who_me_attack'];
        $whoDefenseYou = $movesPieces[$youPieceJaque]['who_me_defiende'];
        
        $boxesOfPieceJaque = $this->getBoxesToPiece($pieceJaque['type'], $pieceLocationOrigin, $pieces['piece-ball']['location']);
        
        $moreCapture = false;
        
        // no hay captura directa y no hay forma de interceptar porque la pieza es un caballo
        if ($pieceJaque['type'] == 3 && empty($whoAttackYou)) {
            if (empty($myAttack)) {
                foreach ($myPieces as $piece) {
                    $moves = $movesPieces[$piece]['moves'];
                    
                    foreach ($moves as $move) {
                        $locationFormat = str_split($move);
                        $resBox = $move;
                        $resPiece = $piece;
                        $resLocation = [
                            'row' => $locationFormat[0],
                            'col' => $locationFormat[1],
                        ];
                        $resMessage = 'Gana el player porque no hay pieza que lo ataque o intercepte y es un caballo';
                        $resAction = 3;
                        $resLine = __LINE__;
                    }
                }
            } else {
                foreach ($myAttack as $piece => $attack) {
                    foreach ($attack as $pieceAttack => $move) {
                        $locationFormat = str_split($move);
                        $resBox = $move;
                        $resPiece = $piece;
                        $resLocation = [
                            'row' => $locationFormat[0],
                            'col' => $locationFormat[1],
                        ];
                        $resMessage = 'Gana el player porque no hay pieza que lo ataque o intercepte y es un caballo';
                        $resAction = 3;
                        $resLine = __LINE__;
                    }
                }
            }
        }
        
        // no se puede capturar directamente buscar interceptaciones
        if (empty($whoAttackYou)) {
            // no hay forma de interceptar gana el jugador
            if (empty($boxesOfPieceJaque)) {
                
                if (empty($myAttack)) {
                    foreach ($myPieces as $piece) {
                        $moves = $movesPieces[$piece]['moves'];
                        
                        foreach ($moves as $move) {
                            $locationFormat = str_split($move);
                            $resBox = $move;
                            $resPiece = $piece;
                            $resLocation = [
                                'row' => $locationFormat[0],
                                'col' => $locationFormat[1],
                            ];
                            $resMessage = 'Gana el player porque no hay pieza que lo ataque o intercepte y la pieza esta al lado del balon';
                            $resAction = 3;
                            $resLine = __LINE__;
                        }
                    }
                } else {
                    foreach ($myAttack as $piece => $attack) {
                        foreach ($attack as $pieceAttack => $move) {
                            $locationFormat = str_split($move);
                            $resBox = $move;
                            $resPiece = $piece;
                            $resLocation = [
                                'row' => $locationFormat[0],
                                'col' => $locationFormat[1],
                            ];
                            $resMessage = 'Gana el player porque no hay pieza que lo ataque o intercepte y la pieza esta al lado del balon';
                            $resAction = 3;
                            $resLine = __LINE__;
                        }
                    }
                }
                
            } else {
                /**
                 * buscar cual pieza puede interceptar y que no la capturen en el siquiente turno,
                 * y si hay varias dar prioridad a la que de jaque en el siguiente turno y esta defendida sino
                 * mover la primera que encuentra defendida.
                 */
                if ( ! empty(array_intersect($allMyMoves, $boxesOfPieceJaque))) {
                    $boxToIntercept = 0;
                    $pieceToIntercept = '';
                    $bestScore = 0;
                    
                    // por cada casilla que hay entre la pieza y el balon
                    foreach ($boxesOfPieceJaque as $box) {
                        foreach ($myPieces as $piece) {
                            $searchPiece = array_search($box, $movesPieces[$piece]['allmoves']);
                            // si intercepta, revisar si da jaque y esta defendida o solo esta defendida
                            if ($searchPiece !== false) {
                                $pieceToMove = $pieces[$piece];
                                $piecesLocTemp = $piecesMap;
                                $piecesLocTemp[$pieceToMove['map']] = $box;
                                $locationTemp = str_split($box);
                                $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'],
                                    ['row' => $locationTemp[0], 'col' => $locationTemp[1]], $pieceBallLoc,
                                    $piecesLocTemp);
                                $isDefende = false;
                                
                                // si esta defendida
                                $myTeam = array_diff($myPieces, [$piece]);
                                foreach ($myTeam as $pteam) {
                                    $pieceTeam = $pieces[$pteam];
                                    if ($this->defensePieceAfterMove($pieceTeam['type'], $pieceTeam['location'],
                                        $box, $piecesLocTemp)) {
                                        $isDefende = true;
                                    }
                                }
                                
                                // hace jaque y esta defendida
                                if ($doJaqueAfterMove && $isDefende) {
                                    $bestScore = 3;
                                    $pieceToIntercept = $piece;
                                    $boxToIntercept = $box;
                                } elseif ( ! $doJaqueAfterMove && $isDefende && $bestScore < 3) {
                                    // intercepta y esta defendida
                                    $bestScore = 2;
                                    $pieceToIntercept = $piece;
                                    $boxToIntercept = $box;
                                } elseif ($bestScore < 2) {
                                    $bestScore = 1;
                                    $pieceToIntercept = $piece;
                                    $boxToIntercept = $box;
                                }
                            }
                        }
                    }
                    
                    $locationFormat = str_split($boxToIntercept);
                    $resBox = $boxToIntercept;
                    $resPiece = $pieceToIntercept;
                    $resLocation = [
                        'row' => $locationFormat[0],
                        'col' => $locationFormat[1],
                    ];
                    $resMessage = 'Mover la pieza para interceptar';
                    $resLine = __LINE__;
                } else {
                    if (empty($myAttack)) {
                        foreach ($myPieces as $piece) {
                            $moves = $movesPieces[$piece]['moves'];
                            
                            foreach ($moves as $move) {
                                $locationFormat = str_split($move);
                                $resBox = $move;
                                $resPiece = $piece;
                                $resLocation = [
                                    'row' => $locationFormat[0],
                                    'col' => $locationFormat[1],
                                ];
                                $resMessage = 'Gana el player porque no hay pieza que lo ataque o intercepte';
                                $resAction = 3;
                                $resLine = __LINE__;
                            }
                        }
                    } else {
                        foreach ($myAttack as $piece => $attack) {
                            foreach ($attack as $pieceAttack => $move) {
                                $locationFormat = str_split($move);
                                $resBox = $move;
                                $resPiece = $piece;
                                $resLocation = [
                                    'row' => $locationFormat[0],
                                    'col' => $locationFormat[1],
                                ];
                                $resMessage = 'Gana el player porque no hay pieza que lo ataque o intercepte';
                                $resAction = 3;
                                $resLine = __LINE__;
                            }
                        }
                    }
                }
            }
        } else {
            /**
             * hay captura directa, encontrar la que da jaque y esta defendida
             */
            if (empty($whoDefenseYou)) {
                // no hay nadie que defienda la pieza
                $pieceToCapture = '';
                $bestScore = 0;
                foreach ($whoAttackYou as $piece => $move) {
                    $pieceToMove = $pieces[$piece];
                    $piecesLocTemp = $piecesMap;
                    $piecesLocTemp[$pieceToMove['map']] = $locationPieceJaque;
                    $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $pieceLocationOrigin, $pieceBallLoc, $piecesLocTemp);
                    
                    if ($doJaqueAfterMove) {
                        $pieceToCapture = $piece;
                        $bestScore = 3;
                    } elseif ($bestScore < 3) {
                        $pieceToCapture = $piece;
                    }
                }
                
                $resBox = $locationPieceJaque;
                $resPiece = $pieceToCapture;
                $resLocation = $pieceLocationOrigin;
                $resMessage = 'Mover la pieza porque nadie la ataca';
                $resLine = __LINE__;
            } elseif (empty($boxesOfPieceJaque)) {
                // no se puede interceptar y hay captura directa
                $pieceToCapture = '';
                $bestScore = 0;
                foreach ($whoAttackYou as $piece => $move) {
                    $pieceToMove = $pieces[$piece];
                    $piecesLocTemp = $piecesMap;
                    $piecesLocTemp[$pieceToMove['map']] = $locationPieceJaque;
                    $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $pieceLocationOrigin, $pieceBallLoc, $piecesLocTemp);
                    
                    $isDefende = false;
                    
                    // si esta defendida
                    $myTeam = array_diff($myPieces, [$piece]);
                    foreach ($myTeam as $pteam) {
                        $pieceTeam = $pieces[$pteam];
                        if ($this->defensePieceAfterMove($pieceTeam['type'], $pieceTeam['location'],
                            $locationPieceJaque, $piecesLocTemp)) {
                            $isDefende = true;
                        }
                    }
                    
                    // hace jaque y esta defendida
                    if ($doJaqueAfterMove && $isDefende) {
                        $bestScore = 3;
                        $pieceToCapture = $piece;
                    } elseif ( ! $doJaqueAfterMove && $isDefende && $bestScore < 3) {
                        // captura y esta defendida
                        $bestScore = 2;
                        $pieceToCapture = $piece;
                    } elseif ($bestScore < 2) {
                        $bestScore = 1;
                        $pieceToCapture = $piece;
                    }
                }
                
                $resBox = $locationPieceJaque;
                $resPiece = $pieceToCapture;
                $resLocation = $pieceLocationOrigin;
                $resMessage = 'Mover la pieza porque no se puede interceptar';
                $resLine = __LINE__;
            } elseif ( ! empty($whoAttackYou)) {
                $boxToBestMove = '';
                $pieceToBestMove = '';
                $bestScore = 0;
                
                if ( ! empty(array_intersect($allMyMoves, $boxesOfPieceJaque))) {
                    // por cada casilla que hay entre la pieza y el balon
                    foreach ($boxesOfPieceJaque as $box) {
                        foreach ($myPieces as $piece) {
                            $searchPiece = array_search($box, $movesPieces[$piece]['allmoves']);
                            // si intercepta, revisar si da jaque y esta defendida o solo esta defendida
                            if ($searchPiece !== false) {
                                $pieceToMove = $pieces[$piece];
                                $piecesLocTemp = $piecesMap;
                                $piecesLocTemp[$pieceToMove['map']] = $box;
                                $locationTemp = str_split($box);
                                $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'],
                                    ['row' => $locationTemp[0], 'col' => $locationTemp[1]], $pieceBallLoc,
                                    $piecesLocTemp);
                                $isDefende = false;
                                
                                // si esta defendida
                                $myTeam = array_diff($myPieces, [$piece]);
                                foreach ($myTeam as $pteam) {
                                    $pieceTeam = $pieces[$pteam];
                                    if ($this->defensePieceAfterMove($pieceTeam['type'], $pieceTeam['location'], $box, $piecesLocTemp)) {
                                        $isDefende = true;
                                    }
                                }
                                
                                // hace jaque y esta defendida
                                if ($doJaqueAfterMove && $isDefende && $bestScore < 6) {
                                    $bestScore = 5;
                                    $pieceToBestMove = $piece;
                                    $boxToBestMove = $box;
                                } elseif ( ! $doJaqueAfterMove && $isDefende && $bestScore < 4) {
                                    // intercepta y esta defendida
                                    $bestScore = 3;
                                    $pieceToBestMove = $piece;
                                    $boxToBestMove = $box;
                                }
                            }
                        }
                    }
                }
                
                foreach ($whoAttackYou as $piece => $move) {
                    $pieceToMove = $pieces[$piece];
                    $piecesLocTemp = $piecesMap;
                    $piecesLocTemp[$pieceToMove['map']] = $locationPieceJaque;
                    $doJaqueAfterMove = $this->getAblyJaqueToCapture($pieceToMove['type'], $pieceLocationOrigin, $pieceBallLoc, $piecesLocTemp);
                    
                    $isDefende = false;
                    
                    // si esta defendida
                    $myTeam = array_diff($myPieces, [$piece]);
                    foreach ($myTeam as $pteam) {
                        $pieceTeam = $pieces[$pteam];
                        if ($this->defensePieceAfterMove($pieceTeam['type'], $pieceTeam['location'], $locationPieceJaque, $piecesLocTemp)) {
                            $isDefende = true;
                        }
                    }
                    
                    // hace jaque y esta defendida
                    if ($doJaqueAfterMove && $isDefende) {
                        $bestScore = 6;
                        $pieceToBestMove = $piece;
                    } elseif ( ! $doJaqueAfterMove && $isDefende && $bestScore < 5) {
                        // captura y esta defendida
                        $bestScore = 4;
                        $pieceToBestMove = $piece;
                    } elseif ($bestScore < 3) {
                        $bestScore = 2;
                        $pieceToBestMove = $piece;
                    }
                    $boxToBestMove = $locationPieceJaque;
                }
                
                $locationFormat = str_split($boxToBestMove);
                $resBox = $boxToBestMove;
                $resPiece = $pieceToBestMove;
                $resLocation = [
                    'row' => $locationFormat[0],
                    'col' => $locationFormat[1],
                ];
                $resMessage = 'Mover la pieza que mejor lo hace entre interceptación y captura';
                $resLine = __LINE__;
            }
        }
        
        return [
            'box' => $resBox,
            'piece' => $resPiece,
            'location' => $resLocation,
            'message' => $resMessage,
            'action' => $resAction,
            'line' => $resLine,
        ];
    }
}