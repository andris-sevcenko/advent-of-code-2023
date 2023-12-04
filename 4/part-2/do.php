<?php
$file = file('../in/input.txt');
$numbers = [];
$row = 0;

function getMatchCount ($ticket) {
    return array_intersect($ticket[0], $ticket[1]);
};

function getAdditionalTicketNumbers ($id, $ticket) {
    $more = getMatchCount($ticket);
    $out = [];
    foreach ($more as $foo) {
        $out[] = ++$id;
    }
    return $out;
}

$ticketDictionary = [];
$pile = [];

foreach ($file as $line) {
    $line = trim($line);
    $parts = explode(":", $line);
    preg_match('/(\d+)/', $parts[0], $matches);

    $numbers = explode("|", $parts[1]);
    $winning = array_filter(explode(' ', $numbers[0]));
    $picked = array_filter(explode(' ', $numbers[1]));

    $pile[$matches[1]] = [1, count(getMatchCount([$winning, $picked]))];
}

$sum = 0;

for ($cardIndex = 1; $cardIndex <= $matches[1]; $cardIndex++) {
    $ticket = $pile[$cardIndex];
    $sum += $ticket[0];

    for ($additional = 0; $additional < $ticket[1]; $additional++) {
        $targetCard = $cardIndex + $additional + 1;
        $pile[$targetCard][0] += $ticket[0];
    }
}
echo $sum;
