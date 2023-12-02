<?php
$file = file('../in/input.txt');
$bag = [
    'red' => 12,
    'green' => 13,
    'blue' => 14,
];

$sum = 0;

foreach ($file as $line) {
    preg_match('/Game (\d+): (.*)/', $line, $matches);
    $id = $matches[1];
    $rounds = explode(';', $matches[2]);
    foreach ($rounds as $round) {
        $draws = explode(',', $round);
        foreach ($draws as $draw) {
            $cubes = explode(' ', trim($draw));
            $color = trim($cubes[1]);
            if ($bag[$color] < $cubes[0]) {
                 continue 3;
            }
        }
    }
    $sum += (int)$id;
}

echo $sum;