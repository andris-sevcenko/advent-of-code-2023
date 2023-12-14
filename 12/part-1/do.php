<?php
function valid($line, $answer) {
    $pattern = '/^\.*';
    $pattern .= implode('\.+', $answer) . '\.*$/';
    return preg_match($pattern, $line);
}

$file = file('../in/input.txt');

$sum = 0;

foreach ($file as $row => $line) {
    $line = trim($line);
    [$test, $answer] = explode(' ', $line);
    $answer = explode(',', $answer);
    foreach ($answer as &$answerPart) {
        $answerPart = str_pad('', $answerPart, '#');
    }

    $wildcardCount = substr_count($test, '?');
    $permutations = 2 ** $wildcardCount;
    $search = array_pad([], $wildcardCount, '/\?/');

    for ($i = 0; $i < $permutations; $i++) {
        $bitmask = str_pad((string) decbin($i), $wildcardCount, '0', STR_PAD_LEFT);

        $replace = str_split(str_replace(['0', '1'], ['.', '#'], $bitmask));
        $result = preg_replace($search, $replace, $test, 1);

        if (valid($result, $answer)) {
            $sum++;
        }
    }
    echo ++$row ."\n";
}

echo $sum;