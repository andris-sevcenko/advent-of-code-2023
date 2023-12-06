<?php
$file = file('../in/input.txt');

$times = preg_split('/\s+/', trim($file[0]));
$dists = preg_split('/\s+/', trim($file[1]));
array_shift($times);
array_shift($dists);

$wins = 1;

for ($race = 0, $raceMax = count($times); $race < $raceMax; $race++) {
    $raceTime = $times[$race];
    $raceDist = $dists[$race];

    $waysToWin = 0;
    for ($hold = 1; $hold < $raceTime; $hold++) {

        $distance = $hold * ($raceTime - $hold);
        if ($distance > $raceDist) {
            $waysToWin++;
        }
    }
    $wins *= $waysToWin;
}

echo $wins;