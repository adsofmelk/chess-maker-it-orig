<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use App\Distribuciones as Distros;

trait ChessTrait
{
    public function buildBoard()
    {
        $bColor = true;
        $board = [];
        
        for ($i = 0; $i < 8; $i++) {
            if ($i % 2 == 0) {
                $bColor = true;
            } else {
                $bColor = false;
            }
            
            for ($j = 0; $j < 8; $j++) {
                $color = 'black';
                $codeColor = 1;
                
                if ($bColor) {
                    $color = 'white';
                    $codeColor = 0;
                    $bColor = false;
                } else {
                    $bColor = true;
                }
                
                $board[] = [
                    'location' => [$i, $j],
                    'color' => $color,
                    'codeColor' => $codeColor,
                    'name' => 'box-'.$i.$j,
                    'piece' => '',
                ];
            }
        }
        
        return $board;
    }
    
    public function buildPieces($distribution = '25,14,15,17,19,26,28')
    {
        $piecesTemp = explode(',', $distribution);
        $typePieces = [4, 1, 2, 3, 1, 2, 3];
        $namePieces = ['piece-ball', 'piece-tw', 'piece-aw', 'piece-hw', 'piece-tb', 'piece-ab', 'piece-hb'];
        $colors = ['none', 'white', 'white', 'white', 'black', 'black', 'black'];
        $pieces = [];
        $piecesMap = [];
        
        for ($i = 0; $i < 7; $i++) {
            $row = (int)($piecesTemp[$i] / 8);
            $col = ((int)($piecesTemp[$i] % 8));
            
            if ($col > 0) {
                $col = $col - 1;
            } elseif ($col == 0) {
                $row = $row - 1;
                $col = 7;
            }
            
            $pieces[$namePieces[$i]] = [
                'type' => $typePieces[$i],
                'location' => [
                    'row' => $row,
                    'col' => $col,
                ],
                'color' => $colors[$i],
                'map' => $i,
            ];
            
            $piecesMap[] = $row.$col;
        }
        
        $piecesNames = $namePieces;
        
        return compact([
            'pieces',
            'piecesMap',
            'piecesNames',
        ]);
    }
    
    /**
     * $args => array params
     * $args['locInitial'] => location initial
     * $args['locFinal']   => location final
     * $args['type']       => type of piece
     * $args['pieces']     => location of pieces
     */
    public function checkMove($args)
    {
        $locInitial = $args['locInitial'];
        $locFinal = $args['locFinal'];
        $type = $args['type'];
        $pieces = $args['pieces'];
        
        $rowInit = $locInitial['row'] * 1;
        $rowEnd = $locFinal['row'] * 1;
        $rowResult = abs($rowInit - $rowEnd);
        
        $colInit = $locInitial['col'] * 1;
        $colEnd = $locFinal['col'] * 1;
        $colResult = abs($colInit - $colEnd);
        
        // Log::warning('========');
        // Log::warning($args);
        // Log::warning('========');
        
        // console.log('location difference', rowResult, yf);
        switch ($type) {
            case 1:
            {
                // tower
                if (($rowResult == 0 || $colResult == 0) && $rowResult != $colResult) {
                    $ejePoints = [$colInit, $colEnd];
                    $ejeFijo = $rowInit;
                    $moveRow = true;
                    
                    if ($rowResult > $colResult) {
                        $ejePoints = [$rowInit, $rowEnd];
                        $ejeFijo = $colInit;
                        $moveRow = false;
                    }
                    
                    $points = $this->calcPoints(intval($ejePoints[0]), intval($ejePoints[1]));
                    
                    if ($moveRow) {
                        foreach ($points as $point) {
                            if (array_search(strval($ejeFijo).strval($point), $args['piecesMap']) !== false) {
                                return false;
                            }
                        }
                        
                        return true;
                    }
                    
                    foreach ($points as $point) {
                        if (array_search(strval($point).strval($ejeFijo), $args['piecesMap']) !== false) {
                            return false;
                        }
                    }
                    
                    return true;
                }
            }
            
            case 2:
            {
                // alfil
                if ($rowResult == $colResult) {
                    $rows = $this->calcPoints(intval($rowInit), intval($rowEnd));
                    $cols = $this->calcPoints(intval($colInit), intval($colEnd));
                    
                    foreach ($rows as $key => $row) {
                        if (array_search(strval($row).strval($cols[$key]), $args['piecesMap']) !== false) {
                            return false;
                        }
                    }
                    
                    return true;
                }
            }
            
            case 3:
            {
                // horse
                if (($rowResult + $colResult) == 3 && $rowResult > 0 && $colResult > 0) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    public function getDistribution($distribution = '')
    {
        $distribution = Distros::select('distribution')
            ->where('distribution', '<>', $distribution)
            ->inRandomOrder()
            ->first()->distribution;
        
        return $distribution;
    }
    
    public function calcPoints($point1, $point2)
    {
        $points = [];
        
        if ($point1 > $point2) {
            for ($i = $point1 - 1; $i > $point2; $i--) {
                $points[] = $i;
            }
        } else {
            for ($i = $point1 + 1; $i < $point2; $i++) {
                $points[] = $i;
            }
        }
        
        return $points;
    }
    
    public function getBoxesToPiece($type, $location1, $location2)
    {
        $boxes = [];
        
        $rowInit = $location1['row'] * 1;
        $colInit = $location1['col'] * 1;
        
        $rowEnd = $location2['row'] * 1;
        $colEnd = $location2['col'] * 1;
        
        switch ($type) {
            case 1:
            {
                $rowResult = abs($rowInit - $rowEnd);
                $colResult = abs($colInit - $colEnd);
                
                $ejePoints = [$colInit, $colEnd];
                $ejeFijo = $rowInit;
                $moveRow = true;
                
                if ($rowResult > $colResult) {
                    $ejePoints = [$rowInit, $rowEnd];
                    $ejeFijo = $colInit;
                    $moveRow = false;
                }
                
                $points = $this->calcPoints(intval($ejePoints[0]), intval($ejePoints[1]));
                
                if ($moveRow) {
                    foreach ($points as $point) {
                        $boxes[] = strval($ejeFijo).strval($point);
                    }
                } else {
                    foreach ($points as $point) {
                        $boxes[] = strval($point).strval($ejeFijo);
                    }
                }
                
                break;
            }
            
            case 2:
            {
                $rows = $this->calcPoints(intval($rowInit), intval($rowEnd));
                $cols = $this->calcPoints(intval($colInit), intval($colEnd));
                
                foreach ($rows as $key => $row) {
                    $boxes[] = strval($row).strval($cols[$key]);
                }
                
                break;
            }
        }
        
        return $boxes;
    }
    
    public function shuffle_assoc($array)
    {
        $newArray = [];
        $keys = array_keys($array);
        
        shuffle($keys);
        
        foreach ($keys as $key) {
            $newArray[$key] = $array[$key];
        }
        
        return $newArray;
    }
}
