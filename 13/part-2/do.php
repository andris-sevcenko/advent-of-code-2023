<?php
$file = file('../in/input.txt');

function gridValue(array $grid) {
    $prevLine = '';
    $folds = [];

    foreach ($grid as $idx => $line) {
        if (empty($prevLine)) {
            $prevLine = $line;
            continue;
        }

        if ($line === $prevLine) {
            if(
                isReflected(array_slice($grid, 0, $idx), array_reverse(array_slice($grid, $idx)))
            ) {
                $folds[] = $idx;
            }
        }

        $prevLine = $line;
    }

    return $folds;
}

function isReflected($arr1, $arr2) {
    if (count($arr2) > count($arr1)) {
        $arr2 = array_slice($arr2, count($arr2) - count($arr1));
    }
    if (count($arr2) < count($arr1)) {
        $arr1 = array_slice($arr1, count($arr1) - count($arr2));
    }


    return $arr2 == $arr1;
}

function flip ($array) {
    $out = [];
    foreach ($array as $r => $row) {
        foreach (str_split($row) as $c => $col) {
            $out[$c][$r] = $col;
        }
    }

    foreach ($out as &$row) {
        $row = implode('', $row);
    }
    return $out;
}

function generateSmudges($grid) {
    $copies = [];
    foreach ($grid as $row => $line) {
        foreach ($grid as $row2 => $line2) {
            if (levenshtein($line, $line2) === 1) {
                $copy = $grid;
                $copy[$row] = $line2;
                $copies[] = $copy;
                $copy = $grid;
                $copy[$row2] = $line;
                $copies[] = $copy;
            }
        }
    }
    return $copies;
}
$grids = [];
$grid = [];

foreach ($file as $row => $line) {
    $line = trim($line);

    if (empty($line)) {
        $grids[] = $grid;
        $grid = [];
    } else {
        $grid[] = $line;
    }
}
$grids[] = $grid;

$sum = 0;
foreach ($grids as $grid) {
    $hor = gridValue($grid, 100);
    $ver = gridValue(flip($grid));

    $gridSum = $hor + $ver;

    $smudges = generateSmudges($grid);
    $flipSmudges = generateSmudges(flip($grid));
    foreach ($flipSmudges as $flipSmudge) {
        $smudges[] = flip($flipSmudge);
    }

    foreach ($smudges as $smudge) {
        $smudgedHor = gridValue($smudge);
        $smudgedVer = gridValue(flip($smudge));

        if (
            ($smudgedHor !== $hor && !empty($smudgedHor))
            || ($smudgedVer !== $ver && !empty($smudgedVer))
        ) {
            $hor = array_diff($smudgedHor, $hor);
            $ver = array_diff($smudgedVer, $ver);
            break;
        }
    }

    foreach ($hor as $ref) {
        $sum += $ref * 100;
    }

    foreach ($ver as $ref) {
        $sum += $ref;
    }
}

echo $sum;