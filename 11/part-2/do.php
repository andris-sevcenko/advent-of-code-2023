<?php
$file = file('../in/input.txt');

$grid = [];
$starCols = [];
$expandRows = [];
$expandCols = [];
foreach ($file as $row => $line) {
    $lineArray = str_split(trim($line));
    $grid[] = $lineArray;
    if (strpos($line, '#') === false) {
        $expandRows[] = $row;
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
        $expandCols[] = $i;
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
        $distance = abs($stars[$i][0] - $stars[$ii][0]) + abs($stars[$i][1] - $stars[$ii][1]);;
        $expanders = 0;

        foreach ($expandCols as $expandCol) {
            if (
                ($expandCol > $stars[$i][1] && $expandCol < $stars[$ii][1])
                || ($expandCol < $stars[$i][1] && $expandCol > $stars[$ii][1])
            ) {
                $expanders++;
            }
        }

        foreach ($expandRows as $expandRow) {
            if (
                ($expandRow > $stars[$i][0] && $expandRow < $stars[$ii][0])
                || ($expandRow < $stars[$i][0] && $expandRow > $stars[$ii][0])
            ) {
                $expanders++;
            }
        }

        $sum += $distance;
        $sum += $expanders * 999999;

    }
}

echo $sum;