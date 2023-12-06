<?php
$file = file('../in/input.txt');
ini_set('memory_limit', '1G');
$sum = 0;
$seeds = [];
$rangeCollections = [];

function expandSeeds($seeds) {
    $allSeeds = [];
    $start = microtime(true);
    foreach (array_chunk($seeds, 2) as $chunk) {
        for ($i = 0; $i < $chunk[1]; $i++) {
            $allSeeds[] = $chunk[0] + $i;
        }
    }

    echo microtime(true) - $start;
    return $allSeeds;
}

class Range {
    public int $ends;
    public function __construct(public int $starts, public int $len, public int $dest)
    {
        $this->ends = $this->starts + $this->len;
    }
    public function inRange(int $number)
    {
        return $number >= $this->starts && $number < $this->ends;
    }
    public function transfer(int $number)
    {
        return $this->dest + ($number - $this->starts);
    }
}

$ranges = [];

foreach ($file as $line) {
    $line = trim($line);
    if (empty($line)) {
        $thisMap = '';
        continue;
    }

    if (str_starts_with($line, 'seeds:')) {
        $parts = explode(':', $line);
        $seeds = explode(' ', $parts[1]);
        $seeds = array_filter($seeds);
        continue;
    }
    if (str_ends_with($line, ':')) {
        $thisMap = $line;
        $rangeCollections[$thisMap] = [];
        continue;
    }
    if (!empty($thisMap)) {
        $parts = explode(' ', $line);
        $range = new Range($parts[1], $parts[2], $parts[0]);
        $rangeCollections[$thisMap][] = $range;
    }
}

$locations = [];
$seedChunks = array_chunk($seeds, 2);

$total = count($seedChunks);
$c = 0;
$location = null;

$chunk = $seedChunks[$argv[1]];
$markerCount = 0;

//foreach ($seedChunks as $chunk) {
    echo "Processing chunk " . ++$c . " of $total\n";
    echo "\tChunk size: " . $chunk[1] . "\n\t";
    $marker = round($chunk[1] / 1000);
    $start = $chunk[0];

    for ($i = 0; $i < $chunk[1]; $i++) {
        $seed = $start + $i;
        foreach ($rangeCollections as $rangeCollection) {
            /** @var Range $range */
            foreach ($rangeCollection as $range) {
                if ($range->inRange($seed)) {
                    $seed = $range->transfer($seed);
                    break;
                }
            }
        }
        if ($i % $marker === 0) {
            if (++$markerCount % 10 == 0) {
                echo "\n";
            }

            echo '.';
        }

        $location = $location ? min($location, $seed) : $seed;
    }
    echo "\nCurrent closest: " . $location . "\n";
//}
