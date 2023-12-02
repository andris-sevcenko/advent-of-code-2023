<?php
$file = file('../in/input.txt');


$sum = 0;

foreach ($file as $line) {
    preg_match('/Game (\d+): (.*)/', $line, $matches);
    $id = $matches[1];
    $rounds = explode(';', $matches[2]);

    $bag = [
        'red' => 0,
        'green' => 0,
        'blue' => 0,
    ];

    foreach ($rounds as $round) {
        $draws = explode(',', $round);

        foreach ($draws as $draw) {
            $cubes = explode(' ', trim($draw));
            $color = trim($cubes[1]);
            $bag[$color] = max($bag[$color], $cubes[0]);
        }
    }

    $sum += array_reduce($bag, fn ($carry, $item) => $carry * $item, 1);
}

echo $sum;