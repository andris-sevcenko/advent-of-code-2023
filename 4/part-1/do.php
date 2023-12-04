<?php
$file = file('../in/input.txt');

$sum = 0;

$numbers = [];
$row = 0;

foreach ($file as $line) {
    $line = trim($line);
    $parts = explode(":", $line);
    $numbers = explode("|", $parts[1]);
    $winning = array_filter(explode(' ', $numbers[0]));
    $ticket = array_filter(explode(' ', $numbers[1]));

    $match = count(array_intersect($winning, $ticket));
    if ($match > 0) {
        $sum += 2 ** ($match - 1);
    }
}

echo $sum;
