<?php
$file = file('../in/input.txt');

$sequence = explode(',', trim($file[0]));

$sum = 0;
$c = count($sequence);
foreach ($sequence as $idx => $hashable) {
    echo $idx + 1 ."  of $c\n";
    $cv = 0;
    foreach (str_split($hashable) as $char) {
        $val = ord($char);
        $cv += $val;
        $cv *= 17;
        $cv %= 256;
    }
    $sum += $cv;
}

echo $sum;