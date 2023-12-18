<?php
$file = file('../in/input.txt');

$sum = 0;
$pos = [0, 0];

$cornerPos = 'BL';
$lineCount = count($file);

foreach ($file as $idx => $line) {
    $nextLine = $file[$idx + 1] ?? null;

    if (!$nextLine) {
        $nextLine = $file[0];
    }

    $line = trim($line);
    $nextLine = trim($nextLine);

    preg_match('/(\w) (\d+) \(#([a-f0-9]+)\)/', $line, $matches);
    [, , , $color] = $matches;
    $direction = match (substr($color, -1)) {
        '0' => 'R',
        '1' => 'D',
        '2' => 'L',
        '3' => 'U',
    };
    $num = hexdec(substr($color, 0, 5));


    preg_match('/(\w) (\d+) \(#([a-f0-9]+)\)/', $nextLine, $matches);
    [, , , $color] = $matches;
    $nextDirection = match (substr($color, -1)) {
        '0' => 'R',
        '1' => 'D',
        '2' => 'L',
        '3' => 'U',
    };

    $xMod = 0;
    $yMod = 0;
    if ($direction === 'R') {
        switch ($cornerPos) {
            case 'TL':
                $cornerPos = $nextDirection === 'U' ? 'TL' : 'TR';
                $xMod += $num + ($nextDirection === 'D' ? 1 : 0);
                break;
            case 'TR':
                $cornerPos = $nextDirection === 'U' ? 'TL' : 'TR';
                $xMod += $num + ($nextDirection === 'D' ? 0 : -1);
                break;
            case 'BL':
                $cornerPos = $nextDirection === 'U' ? 'BR' : 'BL';
                $xMod += $num + ($nextDirection === 'D' ? 0 : 1);
                break;
            case 'BR':
                $cornerPos = $nextDirection === 'U' ? 'BR' : 'BL';
                $xMod += $num + ($nextDirection === 'D' ? -1 : 0);
                break;
        }
    }
    if ($direction === 'L') {
        switch ($cornerPos) {
            case 'TL':
                $cornerPos = $nextDirection === 'U' ? 'TR' : 'TL';
                $xMod -= $num + ($nextDirection === 'D' ? 0 : -1);
                break;
            case 'TR':
                $cornerPos = $nextDirection === 'U' ? 'TR' : 'TL';
                $xMod -= $num + ($nextDirection === 'D' ? 1 : 0);
                break;
            case 'BL':
                $cornerPos = $nextDirection === 'U' ? 'BL' : 'BR';
                $xMod -= $num + ($nextDirection === 'D' ? -1 : 0);
                break;
            case 'BR':
                $cornerPos = $nextDirection === 'U' ? 'BL' : 'BR';
                $xMod -= $num + ($nextDirection === 'D' ? 0 : 1);
                break;
        }
    }
    if ($direction === 'U') {
        switch ($cornerPos) {
            case 'TL':
                $cornerPos = $nextDirection === 'R' ? 'TL' : 'BL';
                $yMod -= $num + ($nextDirection === 'R' ? 0 : -1);
                break;
            case 'TR':
                $cornerPos = $nextDirection === 'R' ? 'BR' : 'TR';
                $yMod -= $num + ($nextDirection === 'R' ? -1 : 0);
                break;
            case 'BL':
                $cornerPos = $nextDirection === 'R' ? 'TL' : 'BL';
                $yMod -= $num + ($nextDirection === 'R' ? 1 : 0);
                break;
            case 'BR':
                $cornerPos = $nextDirection === 'R' ? 'BR' : 'TR';
                $yMod -= $num + ($nextDirection === 'R' ? 0 : 1);
                break;
        }
    }
    if ($direction === 'D') {
        switch ($cornerPos) {
            case 'TL':
                $cornerPos = $nextDirection === 'R' ? 'BL' : 'TL';
                $yMod += $num + ($nextDirection === 'R' ? 1 : 0);
                break;
            case 'TR':
                $cornerPos = $nextDirection === 'R' ? 'TR' : 'BR';
                $yMod += $num + ($nextDirection === 'R' ? 0 : 1);
                break;
            case 'BL':
                $cornerPos = $nextDirection === 'R' ? 'BL' : 'TL';
                $yMod += $num + ($nextDirection === 'R' ? 0 : -1);
                break;
            case 'BR':
                $cornerPos = $nextDirection === 'R' ? 'TR' : 'BR';
                $yMod += $num + ($nextDirection === 'R' ? -1 : 0);
                break;
        }
    }

    $newPos = [$pos[0] + $xMod, $pos[1] + $yMod];

    $sum += $pos[0] * $newPos[1] - $newPos[0] * $pos[1];
    $pos = $newPos;
    echo $idx . ' of ' . $lineCount . "\n";
}

echo $sum / 2;