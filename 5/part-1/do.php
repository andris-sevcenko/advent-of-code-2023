<?php
$file = file('../in/input.txt');

$sum = 0;
$seeds = [];
$rangeCollections = [];

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
foreach ($seeds as $seed) {
    foreach ($rangeCollections as $rangeCollection) {
        /** @var Range $range */
        foreach ($rangeCollection as $range) {
            if ($range->inRange($seed)) {
                $seed = $range->transfer($seed);
                break;
            }
        }
    }
    $locations[] = $seed;
}

echo min(...$locations);
