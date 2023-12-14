<?php
$file = file('../in/input.txt');

function gridValue(array $grid, $mul = 1) {
    $prevLine = '';
    $sum = 0;

    foreach ($grid as $idx => $line) {
        if (empty($prevLine)) {
            $prevLine = $line;
            continue;
        }

        if ($line === $prevLine) {
            if(
                isReflected(array_slice($grid, 0, $idx), array_reverse(array_slice($grid, $idx)))
            ) {
                $sum += $idx * $mul;
            }
        }

        $prevLine = $line;
    }

    return $sum;
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
    $sum += gridValue($grid, 100);
    $sum += gridValue(flip($grid), 1);

}

echo $sum;