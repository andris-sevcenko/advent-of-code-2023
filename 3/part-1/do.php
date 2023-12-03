<?php
$file = file('../in/input.txt');

$sum = 0;
$grid = [];

class Point
{
    public bool $isSymbol;

    public function __construct(public string $val)
    {
        $this->isSymbol = !preg_match('/^[\d.]/', $this->val);
    }
}

$numbers = [];
$row = 0;

foreach ($file as $line) {
    $line = trim($line);
    $chars = str_split($line);
    $col = 0;

    $parsing = false;
    $number = '';
    $markers = [];

    $lineLength = strlen($line);
    foreach ($chars as $char) {
        $grid[$col][$row] = new Point($char);
        $end = $col === $lineLength - 1;

        if (is_numeric($char)) {
            $markers[] = [$col, $row - 1];
            $markers[] = [$col, $row + 1];

            if ($parsing) {
                $number .= $char;
            } else {
                $number = (string)$char;
                $parsing = true;
                $markers[] = [$col - 1, $row];
                $markers[] = [$col - 1, $row - 1];
                $markers[] = [$col - 1, $row + 1];
            }

            if ($end) {
                $markers[] = [$col + 1, $row];
                $markers[] = [$col + 1, $row - 1];
                $markers[] = [$col + 1, $row + 1];
                $numbers[] = [$number, $markers];
                $markers = [];
            }
        } else {
            if ($parsing) {
                $markers[] = [$col, $row];
                $markers[] = [$col, $row - 1];
                $markers[] = [$col, $row + 1];
                $numbers[] = [$number, $markers];
                $markers = [];
            }
            $parsing = false;
        }
        $col++;
    }
    $row++;
}

foreach ($numbers as $numberData) {
    $number = $numberData[0];
    foreach ($numberData[1] as $marker) {
        /** @var Point|null $point */
        $point = $grid[$marker[0]][$marker[1]] ?? null;
        if ($point?->isSymbol) {
            $sum += $number;
            break;
        }
    }
}

echo $sum;
