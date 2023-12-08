<?php
$file = file('../in/input.txt');

$directions = str_split(trim($file[0]));

array_shift($file);
array_shift($file);

$nodes = [];

class Node {
    public function __construct(public string $id, public string $l, public string $r){

    }
}

foreach ($file as $line) {
    if (!preg_match('/^(\w+).*\((\w+), (\w+)\)/', $line, $matches)) {
        die($line);
    }

    $nodes[$matches[1]] = new Node($matches[1], $matches[2], $matches[3]);
}
$idx = 0;
$loop = count($directions) - 1;

$node = $nodes['AAA'];

$steps = 0;
while (true) {
    $step = $directions[$idx];
    if ($step === 'L') {
        $node = $nodes[$node->l];
    } else {
        $node = $nodes[$node->r];
    }
    $steps++;

    if ($node->id === 'ZZZ') {
        break;
    }
    $idx = $idx === $loop ? 0 : $idx + 1;
}

echo $steps;
