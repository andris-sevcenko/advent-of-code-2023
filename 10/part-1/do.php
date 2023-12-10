<?php
$file = file('../in/input.txt');
function getNext($row, $col, $symbol): array {
    switch ($symbol) {
        case 'J':
            return [
                [$row - 1, $col],
                [$row, $col - 1],
            ];
        case '7':
            return [
                [$row + 1, $col],
                [$row, $col - 1],
            ];
        case 'L':
            return [
                [$row - 1, $col],
                [$row, $col + 1],
            ];
        case 'F':
            return [
                [$row + 1, $col],
                [$row, $col + 1],
            ];
        case '|':
            return [
                [$row + 1, $col],
                [$row -1 , $col],
            ];
        case '-':
            return [
                [$row, $col - 1],
                [$row, $col + 1],
            ];
    }
    throw new Exception('oops?');
}

class Grid {
    private array $grid = [];
    private array $steps = [];
    public function addCell($row, $col, $cell) {
        $this->grid[$row][$col] = $cell;
    }

    public function getCell($row, $col) {
        return $this->grid[$row][$col];
    }

    public function registerStep($row, $col) {
        $this->steps[$row . '.' . $col] = true;
    }
    public function printGrid() {
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $cell) {
                $cell = strtr($cell, [
                    '-' => '━',
                    '|' => '┃',
                    'F' => '┏',
                    '7' => '┓',
                    'L' => '┗',
                    'J' => '┛',
                    'S' => '┛'
                ]);
                if (!empty($this->steps[$row . '.' . $col])) {
                    echo "\033[1;31m" . $cell . "\033[0m";
                } else {
                    echo '*';
                }
            }
            echo "\n";
        }
    }
}

$grid = new Grid();
foreach ($file as $row => $line) {
    foreach (str_split(trim($line)) as $col => $char) {
        $grid->addCell($row, $col, $char);
        if ($char === 'S') {
            $start = [$row, $col];
        }
    }
}
$path = [];
$steps = 1;

print_r($start);
$previousStep = $start;
$nextStep = [$start[0], $start[1] - 1];
$grid->registerStep(...$nextStep);

$next = $nextStep;
$path[$steps] = $next;

$counter = 0;

while ($counter++ < 20000) {
    $cell = $grid->getCell($nextStep[0], $nextStep[1]);
    $nextOptions = getNext($nextStep[0], $nextStep[1], $cell);
    $moveTo = $nextOptions[0] === $previousStep ? $nextOptions[1] : $nextOptions[0];
    $path[++$steps] = $moveTo;
    $grid->registerStep(...$moveTo);
    $previousStep = $nextStep;
    $nextStep = $moveTo;
    if ($grid->getCell(...$moveTo) === 'S') {
        break;
    };
}
$grid->printGrid();
$furthest = floor(count($path) / 2);
echo $furthest;
