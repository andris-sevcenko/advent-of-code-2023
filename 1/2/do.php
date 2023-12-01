<?php
$file = file('../in/input.txt');

$sum = 0;
$map = [
    'one' => '1',
    'two' => '2',
    'three' => '3',
    'four' => '4',
    'five' => '5',
    'six' => '6',
    'seven' => '7',
    'eight' => '8',
    'nine' => '9',
];

foreach ($file as $line) {
    $num = 0;

    if (preg_match('/^[^\d]*?([\d]{1}|one|two|three|four|five|six|seven|eight|nine).*([\d]{1}|one|two|three|four|five|six|seven|eight|nine)[^\d]*$/', $line, $matches)) {
        $num = strtr($matches[1] . $matches[2], $map);
    } else {
        echo '-=';
        preg_match('/^[^\d]*?([\d]{1}|one|two|three|four|five|six|seven|eight|nine)[^\d]*$/', $line, $matches);
        $num = strtr($matches[1].$matches[1], $map);
    }

    echo $line ."\n" . $num ."\n";
    $sum += $num;
}

echo $sum;