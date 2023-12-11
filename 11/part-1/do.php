<?php
$file = file('../in/input.txt');

$grid = [];
$starCols = [];
foreach ($file as $row => $line) {
    $lineArray = str_split(trim($line));
    $grid[] = $lineArray;
    if (strpos($line, '#') === false) {
        $grid[] = $lineArray;
    } else {
        foreach ($lineArray as $col => $char) {
            if ($char === '#') {
                $starCols[] = $col;
            }
        }
    }
}
$starCols = array_unique($starCols);

for ($i = count($lineArray) - 1; $i >= 0; $i--) {
    if (!in_array($i, $starCols, true)) {
        foreach ($grid as &$line) {
            array_splice($line, $i, 0, ['.']);
        }
    }
}

$stars = [];
foreach ($grid as $r => $row) {
    foreach ($row as $c => $col) {
        if ($col === '#') {
            $stars[] = [$r, $c];
        }
    }
}

$sum = 0;
for ($i = 0, $iMax = count($stars); $i < $iMax; $i++) {
    for ($ii = $i + 1; $ii < $iMax; $ii++) {
        $sum += abs($stars[$i][0] - $stars[$ii][0]) + abs($stars[$i][1] - $stars[$ii][1]);

    }
}

echo $sum;