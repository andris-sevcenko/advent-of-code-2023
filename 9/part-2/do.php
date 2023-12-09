<?php
$file = file('../in/input.txt');

function getPrevious(array $line): int
{
    $nextLevel = [];
    for ($i = count($line) - 1; $i > 0; $i--) {
        $previousNum = (int)$line[$i] - (int)$line[$i - 1];
        array_unshift($nextLevel, $previousNum);
    }
    if (count(array_unique($nextLevel)) === 1) {
        return $line[0] - $nextLevel[0];;
    }

    return $line[0] - getPrevious($nextLevel);
}
$sum = 0;

foreach ($file as $line) {
    $sum += getPrevious(explode(" ", trim($line)));
}

echo $sum;