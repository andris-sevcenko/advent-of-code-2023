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

    echo sprintf("Race distance %d, record %d\n", $raceDist, $raceTime);

    $waysToWin = 0;
    for ($hold = 1; $hold < $raceTime; $hold++) {
        if ($hold % 1000000 == 0) {
            echo $hold."\n";
        }
        $distance = $hold * ($raceTime - $hold);
        if ($distance > $raceDist) {
//            echo sprintf("Win by holding for %d, speed is %d, total distance is %d\n", $hold, $hold, $distance);
            $waysToWin++;
        }
    }
    $wins *= $waysToWin;
}

echo $wins;