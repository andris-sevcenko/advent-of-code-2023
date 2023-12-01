<?php
$file = file('../in/input.txt');
$sum = 0;

foreach ($file as $line) {
    $num = 0;

    if (preg_match('/^[^\d]*([\d]{1}).*([\d]{1})[^\d]*$/', $line, $matches)) {
        $num = $matches[1] . $matches[2];
    } else {
        preg_match('/^[^\d]*([\d]{1})[^\d]*$/', $line, $matches);
        $num = $matches[1].$matches[1];
    }

    echo $line ."\n" . $num ."\n";
    $sum += $num;
}

echo $sum;