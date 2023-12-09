<?php
$file = file('../in/input.txt');

function getNext(array $line): int
{
    $nextLevel = [];
    for ($i = 0, $iMax = count($line); $i < $iMax - 1; $i++) {
        $nextNum = (int)$line[$i + 1] - (int)$line[$i];
        $nextLevel[] = $nextNum;
    }
    if (count(array_unique($nextLevel)) === 1) {
        return $line[$i] + $nextLevel[1];
    }

    return $line[$i] + getNext($nextLevel);
}

$sum = 0;

foreach ($file as $line) {
    $sum += getNext(explode(" ", trim($line)));
}

echo $sum;