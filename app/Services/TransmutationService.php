<?php
namespace App\Services;

class TransmutationService
{
   
     public static function getTransmutationTable()
    {
        return [
            ['range_start' => 0.0, 'range_end' => 6.9, 'official_grade' => 65],
            ['range_start' => 7.0, 'range_end' => 13.9, 'official_grade' => 66],
            ['range_start' => 14.0, 'range_end' => 20.9, 'official_grade' => 67],
            ['range_start' => 21.0, 'range_end' => 27.9, 'official_grade' => 68],
            ['range_start' => 28.0, 'range_end' => 34.9, 'official_grade' => 69],
            ['range_start' => 35.0, 'range_end' => 41.9, 'official_grade' => 70],
            ['range_start' => 42.0, 'range_end' => 48.9, 'official_grade' => 71],
            ['range_start' => 49.0, 'range_end' => 55.9, 'official_grade' => 72],
            ['range_start' => 56.0, 'range_end' => 62.9, 'official_grade' => 73],
            ['range_start' => 63.0, 'range_end' => 69.9, 'official_grade' => 74],
            ['range_start' => 70.0, 'range_end' => 71.1, 'official_grade' => 75],
            ['range_start' => 71.2, 'range_end' => 72.3, 'official_grade' => 76],
            ['range_start' => 72.4, 'range_end' => 73.5, 'official_grade' => 77],
            ['range_start' => 73.6, 'range_end' => 74.7, 'official_grade' => 78],
            ['range_start' => 74.8, 'range_end' => 75.9, 'official_grade' => 79],
            ['range_start' => 76.0, 'range_end' => 77.1, 'official_grade' => 80],
            ['range_start' => 77.2, 'range_end' => 78.3, 'official_grade' => 81],
            ['range_start' => 78.4, 'range_end' => 79.5, 'official_grade' => 82],
            ['range_start' => 79.6, 'range_end' => 80.7, 'official_grade' => 83],
            ['range_start' => 80.8, 'range_end' => 81.9, 'official_grade' => 84],
            ['range_start' => 82.0, 'range_end' => 83.1, 'official_grade' => 85],
            ['range_start' => 83.2, 'range_end' => 84.3, 'official_grade' => 86],
            ['range_start' => 84.4, 'range_end' => 85.5, 'official_grade' => 87],
            ['range_start' => 85.6, 'range_end' => 86.7, 'official_grade' => 88],
            ['range_start' => 86.8, 'range_end' => 87.9, 'official_grade' => 89],
            ['range_start' => 88.0, 'range_end' => 89.1, 'official_grade' => 90],
            ['range_start' => 89.2, 'range_end' => 90.3, 'official_grade' => 91],
            ['range_start' => 90.4, 'range_end' => 91.5, 'official_grade' => 92],
            ['range_start' => 91.6, 'range_end' => 92.7, 'official_grade' => 93],
            ['range_start' => 92.8, 'range_end' => 93.9, 'official_grade' => 94],
            ['range_start' => 94.0, 'range_end' => 95.1, 'official_grade' => 95],
            ['range_start' => 95.2, 'range_end' => 96.3, 'official_grade' => 96],
            ['range_start' => 96.4, 'range_end' => 97.5, 'official_grade' => 97],
            ['range_start' => 97.6, 'range_end' => 98.7, 'official_grade' => 98],
            ['range_start' => 98.8, 'range_end' => 100.0, 'official_grade' => 99],
        ];
    }



    public static  function getLabTransmutationTable($totalMaxPointsLab)
    {
        //// Calculate LPS
        $LPS = 0.7 * $totalMaxPointsLab;

        //// Calculate HINT
        $HINT = ($totalMaxPointsLab - $LPS) / 25;

        //// Calculate LINT
        $LINT = ($totalMaxPointsLab - ($totalMaxPointsLab - $LPS)) / 10;

        $ranges = [
            
            ['range_start' => $aa = round($totalMaxPointsLab - $HINT, 1), 'range_end' => $totalMaxPointsLab,'official_grade' => 99],
            ['range_start' => $ab = round($aa - $HINT, 1), 'range_end' => $ba = $aa - 0.1, 'official_grade' => 98],
            ['range_start' => $ac = round($ab - $HINT, 1), 'range_end' => $bb = $ab - 0.1, 'official_grade' => 97],
            ['range_start' => $ad = round($ac - $HINT, 1), 'range_end' => $bc = $ac - 0.1, 'official_grade' => 96],
            ['range_start' => $ae = round($ad - $HINT, 1), 'range_end' => $bd = $ad - 0.1, 'official_grade' => 95],
            ['range_start' => $af = round($ae - $HINT, 1), 'range_end' => $be = $ae - 0.1, 'official_grade' => 94],
            ['range_start' => $ag = round($af - $HINT, 1), 'range_end' => $bf = $af - 0.1, 'official_grade' => 93],
            ['range_start' => $ah = round($ag - $HINT, 1), 'range_end' => $bg = $ag - 0.1, 'official_grade' => 92],
            ['range_start' => $ai = round($ah - $HINT, 1), 'range_end' => $bh = $ah - 0.1, 'official_grade' => 91],
            ['range_start' => $aj = round($ai - $HINT, 1), 'range_end' => $bi = $ai - 0.1, 'official_grade' => 90],
            ['range_start' => $ak = round($aj - $HINT, 1), 'range_end' => $bj = $aj - 0.1, 'official_grade' => 89],
            ['range_start' => $al = round($ak - $HINT, 1), 'range_end' => $bk = $ak - 0.1, 'official_grade' => 88],
            ['range_start' => $am = round($al - $HINT, 1), 'range_end' => $bl = $al - 0.1, 'official_grade' => 87],
            ['range_start' => $an = round($am - $HINT, 1), 'range_end' => $bm = $am - 0.1, 'official_grade' => 86],
            ['range_start' => $ao = round($an - $HINT, 1), 'range_end' => $bn = $an - 0.1, 'official_grade' => 85],
            ['range_start' => $ap = round($ao - $HINT, 1), 'range_end' => $bo = $ao - 0.1, 'official_grade' => 84],
            ['range_start' => $aq = round($ap - $HINT, 1), 'range_end' => $bp = $ap - 0.1, 'official_grade' => 83],
            ['range_start' => $ar = round($aq - $HINT, 1), 'range_end' => $bq = $aq - 0.1, 'official_grade' => 82],
            ['range_start' => $as = round($ar - $HINT, 1), 'range_end' => $br = $ar - 0.1, 'official_grade' => 81],
            ['range_start' => $at = round($as - $HINT, 1), 'range_end' => $bs = $as - 0.1, 'official_grade' => 80],
            ['range_start' => $au = round($at - $HINT, 1), 'range_end' => $bt = $at - 0.1, 'official_grade' => 79],
            ['range_start' => $av = round($au - $HINT, 1), 'range_end' => $bu = $au - 0.1, 'official_grade' => 78],
            ['range_start' => $aw = round($av - $HINT, 1), 'range_end' => $bv = $av - 0.1, 'official_grade' => 77],
            ['range_start' => $ax = round($aw - $HINT, 1), 'range_end' => $bw = $aw - 0.1, 'official_grade' => 76],
            ['range_start' => $LPS,           'range_end' => $bx = $ax - 0.1, 'official_grade' => 75],
            ['range_start' => $az = round($LPS - $LINT, 1), 'range_end' => $by = $LPS - 0.1, 'official_grade' => 74],
            ['range_start' => $a1 = round($az - $LINT, 1), 'range_end' => $bz = $az - 0.1, 'official_grade' => 73],
            ['range_start' => $a2 = round($a1 - $LINT, 1), 'range_end' => $b1 = $a1 - 0.1, 'official_grade' => 72],
            ['range_start' => $a3 = round($a2 - $LINT, 1), 'range_end' => $b2 = $a2 - 0.1, 'official_grade' => 71],
            ['range_start' => $a4 = round($a3 - $LINT, 1), 'range_end' => $b3 = $a3 - 0.1, 'official_grade' => 70],
            ['range_start' => $a5 = round($a4 - $LINT, 1), 'range_end' => $b4 = $a4 - 0.1, 'official_grade' => 69],
            ['range_start' => $a6 = round($a5 - $LINT, 1), 'range_end' => $b5 = $a5 - 0.1, 'official_grade' => 68],
            ['range_start' => $a7 = round($a6 - $LINT, 1), 'range_end' => $b6 = $a6 - 0.1, 'official_grade' => 67],
            ['range_start' => $a8 = round($a7 - $LINT, 1), 'range_end' => $b7 = $a7 - 0.1, 'official_grade' => 66],
            ['range_start' => 0.0, 'range_end' => $b8 = $a8 - 0.1, 'official_grade' => 65],
            
            
        ];


        return $ranges;
    }
                                
       
    public static  function getLabTransmutationTableMidterms($totalMaxPointsLabMidterms)
    {
        //// Calculate LPS
        $LPS = 0.7 * $totalMaxPointsLabMidterms;

        //// Calculate HINT
        $HINT = ($totalMaxPointsLabMidterms - $LPS) / 25;

        //// Calculate LINT
        $LINT = ($totalMaxPointsLabMidterms - ($totalMaxPointsLabMidterms - $LPS)) / 10;

        $ranges = [
            
            ['range_start' => $aa = round($totalMaxPointsLabMidterms - $HINT, 1), 'range_end' => $totalMaxPointsLabMidterms,'official_grade' => 99],
            ['range_start' => $ab = round($aa - $HINT, 1), 'range_end' => $ba = $aa - 0.1, 'official_grade' => 98],
            ['range_start' => $ac = round($ab - $HINT, 1), 'range_end' => $bb = $ab - 0.1, 'official_grade' => 97],
            ['range_start' => $ad = round($ac - $HINT, 1), 'range_end' => $bc = $ac - 0.1, 'official_grade' => 96],
            ['range_start' => $ae = round($ad - $HINT, 1), 'range_end' => $bd = $ad - 0.1, 'official_grade' => 95],
            ['range_start' => $af = round($ae - $HINT, 1), 'range_end' => $be = $ae - 0.1, 'official_grade' => 94],
            ['range_start' => $ag = round($af - $HINT, 1), 'range_end' => $bf = $af - 0.1, 'official_grade' => 93],
            ['range_start' => $ah = round($ag - $HINT, 1), 'range_end' => $bg = $ag - 0.1, 'official_grade' => 92],
            ['range_start' => $ai = round($ah - $HINT, 1), 'range_end' => $bh = $ah - 0.1, 'official_grade' => 91],
            ['range_start' => $aj = round($ai - $HINT, 1), 'range_end' => $bi = $ai - 0.1, 'official_grade' => 90],
            ['range_start' => $ak = round($aj - $HINT, 1), 'range_end' => $bj = $aj - 0.1, 'official_grade' => 89],
            ['range_start' => $al = round($ak - $HINT, 1), 'range_end' => $bk = $ak - 0.1, 'official_grade' => 88],
            ['range_start' => $am = round($al - $HINT, 1), 'range_end' => $bl = $al - 0.1, 'official_grade' => 87],
            ['range_start' => $an = round($am - $HINT, 1), 'range_end' => $bm = $am - 0.1, 'official_grade' => 86],
            ['range_start' => $ao = round($an - $HINT, 1), 'range_end' => $bn = $an - 0.1, 'official_grade' => 85],
            ['range_start' => $ap = round($ao - $HINT, 1), 'range_end' => $bo = $ao - 0.1, 'official_grade' => 84],
            ['range_start' => $aq = round($ap - $HINT, 1), 'range_end' => $bp = $ap - 0.1, 'official_grade' => 83],
            ['range_start' => $ar = round($aq - $HINT, 1), 'range_end' => $bq = $aq - 0.1, 'official_grade' => 82],
            ['range_start' => $as = round($ar - $HINT, 1), 'range_end' => $br = $ar - 0.1, 'official_grade' => 81],
            ['range_start' => $at = round($as - $HINT, 1), 'range_end' => $bs = $as - 0.1, 'official_grade' => 80],
            ['range_start' => $au = round($at - $HINT, 1), 'range_end' => $bt = $at - 0.1, 'official_grade' => 79],
            ['range_start' => $av = round($au - $HINT, 1), 'range_end' => $bu = $au - 0.1, 'official_grade' => 78],
            ['range_start' => $aw = round($av - $HINT, 1), 'range_end' => $bv = $av - 0.1, 'official_grade' => 77],
            ['range_start' => $ax = round($aw - $HINT, 1), 'range_end' => $bw = $aw - 0.1, 'official_grade' => 76],
            ['range_start' => $LPS,           'range_end' => $bx = $ax - 0.1, 'official_grade' => 75],
            ['range_start' => $az = round($LPS - $LINT, 1), 'range_end' => $by = $LPS - 0.1, 'official_grade' => 74],
            ['range_start' => $a1 = round($az - $LINT, 1), 'range_end' => $bz = $az - 0.1, 'official_grade' => 73],
            ['range_start' => $a2 = round($a1 - $LINT, 1), 'range_end' => $b1 = $a1 - 0.1, 'official_grade' => 72],
            ['range_start' => $a3 = round($a2 - $LINT, 1), 'range_end' => $b2 = $a2 - 0.1, 'official_grade' => 71],
            ['range_start' => $a4 = round($a3 - $LINT, 1), 'range_end' => $b3 = $a3 - 0.1, 'official_grade' => 70],
            ['range_start' => $a5 = round($a4 - $LINT, 1), 'range_end' => $b4 = $a4 - 0.1, 'official_grade' => 69],
            ['range_start' => $a6 = round($a5 - $LINT, 1), 'range_end' => $b5 = $a5 - 0.1, 'official_grade' => 68],
            ['range_start' => $a7 = round($a6 - $LINT, 1), 'range_end' => $b6 = $a6 - 0.1, 'official_grade' => 67],
            ['range_start' => $a8 = round($a7 - $LINT, 1), 'range_end' => $b7 = $a7 - 0.1, 'official_grade' => 66],
            ['range_start' => 0.0, 'range_end' => $b8 = $a8 - 0.1, 'official_grade' => 65],
            
            
        ];


        return $ranges;
    }

     public static  function getLabTransmutationTableFinals($totalMaxPointsLabFinals)
    {
        //// Calculate LPS
        $LPS = 0.7 * $totalMaxPointsLabFinals;

        //// Calculate HINT
        $HINT = ($totalMaxPointsLabFinals - $LPS) / 25;

        //// Calculate LINT
        $LINT = ($totalMaxPointsLabFinals - ($totalMaxPointsLabFinals - $LPS)) / 10;

        $ranges = [
            
            ['range_start' => $aa = round($totalMaxPointsLabFinals - $HINT, 1), 'range_end' => $totalMaxPointsLabFinals,'official_grade' => 99],
            ['range_start' => $ab = round($aa - $HINT, 1), 'range_end' => $ba = $aa - 0.1, 'official_grade' => 98],
            ['range_start' => $ac = round($ab - $HINT, 1), 'range_end' => $bb = $ab - 0.1, 'official_grade' => 97],
            ['range_start' => $ad = round($ac - $HINT, 1), 'range_end' => $bc = $ac - 0.1, 'official_grade' => 96],
            ['range_start' => $ae = round($ad - $HINT, 1), 'range_end' => $bd = $ad - 0.1, 'official_grade' => 95],
            ['range_start' => $af = round($ae - $HINT, 1), 'range_end' => $be = $ae - 0.1, 'official_grade' => 94],
            ['range_start' => $ag = round($af - $HINT, 1), 'range_end' => $bf = $af - 0.1, 'official_grade' => 93],
            ['range_start' => $ah = round($ag - $HINT, 1), 'range_end' => $bg = $ag - 0.1, 'official_grade' => 92],
            ['range_start' => $ai = round($ah - $HINT, 1), 'range_end' => $bh = $ah - 0.1, 'official_grade' => 91],
            ['range_start' => $aj = round($ai - $HINT, 1), 'range_end' => $bi = $ai - 0.1, 'official_grade' => 90],
            ['range_start' => $ak = round($aj - $HINT, 1), 'range_end' => $bj = $aj - 0.1, 'official_grade' => 89],
            ['range_start' => $al = round($ak - $HINT, 1), 'range_end' => $bk = $ak - 0.1, 'official_grade' => 88],
            ['range_start' => $am = round($al - $HINT, 1), 'range_end' => $bl = $al - 0.1, 'official_grade' => 87],
            ['range_start' => $an = round($am - $HINT, 1), 'range_end' => $bm = $am - 0.1, 'official_grade' => 86],
            ['range_start' => $ao = round($an - $HINT, 1), 'range_end' => $bn = $an - 0.1, 'official_grade' => 85],
            ['range_start' => $ap = round($ao - $HINT, 1), 'range_end' => $bo = $ao - 0.1, 'official_grade' => 84],
            ['range_start' => $aq = round($ap - $HINT, 1), 'range_end' => $bp = $ap - 0.1, 'official_grade' => 83],
            ['range_start' => $ar = round($aq - $HINT, 1), 'range_end' => $bq = $aq - 0.1, 'official_grade' => 82],
            ['range_start' => $as = round($ar - $HINT, 1), 'range_end' => $br = $ar - 0.1, 'official_grade' => 81],
            ['range_start' => $at = round($as - $HINT, 1), 'range_end' => $bs = $as - 0.1, 'official_grade' => 80],
            ['range_start' => $au = round($at - $HINT, 1), 'range_end' => $bt = $at - 0.1, 'official_grade' => 79],
            ['range_start' => $av = round($au - $HINT, 1), 'range_end' => $bu = $au - 0.1, 'official_grade' => 78],
            ['range_start' => $aw = round($av - $HINT, 1), 'range_end' => $bv = $av - 0.1, 'official_grade' => 77],
            ['range_start' => $ax = round($aw - $HINT, 1), 'range_end' => $bw = $aw - 0.1, 'official_grade' => 76],
            ['range_start' => $LPS,           'range_end' => $bx = $ax - 0.1, 'official_grade' => 75],
            ['range_start' => $az = round($LPS - $LINT, 1), 'range_end' => $by = $LPS - 0.1, 'official_grade' => 74],
            ['range_start' => $a1 = round($az - $LINT, 1), 'range_end' => $bz = $az - 0.1, 'official_grade' => 73],
            ['range_start' => $a2 = round($a1 - $LINT, 1), 'range_end' => $b1 = $a1 - 0.1, 'official_grade' => 72],
            ['range_start' => $a3 = round($a2 - $LINT, 1), 'range_end' => $b2 = $a2 - 0.1, 'official_grade' => 71],
            ['range_start' => $a4 = round($a3 - $LINT, 1), 'range_end' => $b3 = $a3 - 0.1, 'official_grade' => 70],
            ['range_start' => $a5 = round($a4 - $LINT, 1), 'range_end' => $b4 = $a4 - 0.1, 'official_grade' => 69],
            ['range_start' => $a6 = round($a5 - $LINT, 1), 'range_end' => $b5 = $a5 - 0.1, 'official_grade' => 68],
            ['range_start' => $a7 = round($a6 - $LINT, 1), 'range_end' => $b6 = $a6 - 0.1, 'official_grade' => 67],
            ['range_start' => $a8 = round($a7 - $LINT, 1), 'range_end' => $b7 = $a7 - 0.1, 'official_grade' => 66],
            ['range_start' => 0.0, 'range_end' => $b8 = $a8 - 0.1, 'official_grade' => 65],
            
            
        ];


        return $ranges;
    }
}


      