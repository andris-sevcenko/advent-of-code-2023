<?php
$file = file('../in/input.txt');

$hands = [];
$handNames = [
    't' => 'High card',
    'u' => 'Pair',
    'v' => 'Two pair',
    'w' => 'Three of a kind',
    'x' => 'full house',
    'y' => 'Four of a kind',
    'z' => 'Five of a kind',
];

/**
 * Convert a hand to a comparable string.
 * 23456789TJQKA => abcdefghijklm
 * Also add a character in front, based on hand strength
 *
 * t - high
 * u - pair
 * v - 2 pair
 * w - 3 of a kind
 * x - full house
 * y - four of a kind
 * z - five of a kind
 * @param string $hand
 * @return void
 */
function transformHand(string $hand): string {
    $hand = strtr($hand, [
        '2' => 'a',
        '3' => 'b',
        '4' => 'c',
        '5' => 'd',
        '6' => 'e',
        '7' => 'f',
        '8' => 'g',
        '9' => 'h',
        'T' => 'i',
        'J' => 'j',
        'Q' => 'k',
        'K' => 'l',
        'A' => 'm',
    ]);

    $cards = [];
    for ($i = 0; $i < 5; $i++) {
        $card = $hand[$i];
        $cards[$card] = !empty($cards[$card]) ? $cards[$card] + 1 : 1;
    }
    arsort($cards);

    $pairs = 0;

    $rank = '';
    foreach ($cards as $card => $count) {
        if ($count === 5) {
            $rank = 'z';
            break;
        }
        if ($count === 4) {
            $rank = 'y';
            break;
        }
        if (count($cards) === 2) {
            $rank = 'x';
            break;
        }
        if ($count === 3) {
            $rank = 'w';
            break;
        }

        if ($count === 2) {
            $pairs++;
        }
    }

    if (empty($rank)) {
        if ($pairs === 2) {
            $rank = 'v';
        } else if ($pairs === 1) {
            $rank = 'u';
        } else {
            $rank = 't';
        }
    }

    return $rank . $hand;
}

$hands = [];
foreach ($file as $line) {
    $parts= explode(' ', $line);
    $bet = trim($parts[1]);
    $hand = transformHand($parts[0]);
    $hands[$hand] = [$bet, $parts[0]];
}

ksort($hands);
$sum = 0;
$idx = 1;
foreach ($hands as $hand => $value) {
    $points = $value[0] * $idx;
    echo $idx . ': ' . $value[1] . ' [' . strtr($hand[0], $handNames). '] - ' . $value[0] . ")\n";
    $sum += $points;
    $idx++;
}
echo $sum;