<?php
$file = file('../in/input.txt');

$sequence = explode(',', trim($file[0]));
$boxes = [];

$sum = 0;
$c = count($sequence);
foreach ($sequence as $idx => $hashable) {
    $cv = 0;
    preg_match('/([\w]+)(.+)/', $hashable, $matches);

    $lens = $matches[1];
    foreach (str_split($lens) as $char) {
        $val = ord($char);
        $cv += $val;
        $cv *= 17;
        $cv %= 256;
    }
    $boxNum = $cv;
    $operation = $matches[2];

    if ($operation === '-') {
        unset($boxes[$boxNum][$lens]);
    } else {
        [,$focus] = explode('=', $operation);
        $boxes[$boxNum][$lens] = $focus;
    }
}

$sum = 0;
foreach ($boxes as $boxNum => $lenses) {
    $idx = 0;
    foreach ($lenses as $lens => $focus) {
        $idx++;
        $sum += ($boxNum + 1) * $idx * $focus;
    }
}

echo $sum;