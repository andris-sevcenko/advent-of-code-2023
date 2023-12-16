<?php
$file = file('../in/input.txt');

class Grid
{
    private array $grid = [];
    public array $energized = [];
    public array $traversed = [];

    public function setCell($row, $col, $cell)
    {
        $this->grid[$row][$col] = $cell;
    }

    public function beam($row, $col, $direction)
    {
        // off-grid
        if (!isset($this->grid[$row][$col])) {
            return null;
        }

        // traversed in this direction
        $signature = $row . '-' . $col . '-' . $direction;
        if (!empty($this->traversed[$signature])) {
            return null;
        }
        $this->traversed[$signature] = true;

        // mark as energized
        $this->energized[$row.'-'.$col] = true;

        // figure out what happens
        $cell = $this->grid[$row][$col];
        if ($cell === '.') {
            return $direction;
        }

        switch ($cell) {
            case '/':
                $newDirection = match($direction) {
                    'R' => 'U',
                    'U' => 'R',
                    'D' => 'L',
                    'L' => 'D',
                };
                break;
            case '\\':
                $newDirection = match($direction) {
                    'R' => 'D',
                    'D' => 'R',
                    'U' => 'L',
                    'L' => 'U',
                };
                break;
            case '-':
                $newDirection = match($direction) {
                    'R' => 'R',
                    'U', 'D' => 'LR',
                    'L' => 'L',
                };
                break;
            case '|':
                $newDirection = match($direction) {
                    'U' => 'U',
                    'L', 'R' => 'UD',
                    'D' => 'D',
                };
                break;
        }

        return $newDirection;
    }
    public function printGrid() {
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $cell) {
                if (!empty($this->energized[$row.'-'.$col])) {
                    echo '#';
                } else {
                    echo $cell;
                }
            }
            echo "\n";
        }
    }
}
class Beam
{
    public function __construct(protected Grid $grid, public $row, public $col, public $direction){

    }

    public function move() {
        switch ($this->direction) {
            case 'R':
                $nextRow = $this->row;
                $nextCol = $this->col + 1;
                break;
            case 'L':
                $nextRow = $this->row;
                $nextCol = $this->col -1;
                break;
            case 'U':
                $nextRow = $this->row - 1;
                $nextCol = $this->col;
                break;
            case 'D':
                $nextRow = $this->row + 1;
                $nextCol = $this->col;
                break;
        }

        $this->row = $nextRow;
        $this->col = $nextCol;

        // Will be a direction, two directions or null;
        return $this->grid->beam($this->row, $this->col, $this->direction);
    }
}
$grid = new Grid();

foreach ($file as $row => $line) {
    $line = trim($line);
    foreach (str_split($line) as $col => $char) {
        $grid->setCell($row, $col, $char);
    }
}

$configs = [];
const GRID = 110;
for ($i = 0; $i < GRID; $i++) {
    $configs[] = [-1, $i, 'D'];
    $configs[] = [GRID, $i, 'U'];
    $configs[] = [$i, -1, 'R'];
    $configs[] = [$i, GRID, 'L'];
}
$configCount = count($configs);
$max = 0;

foreach ($configs as $idx => $config) {
    $grid->energized = [];
    $grid->traversed = [];
    $beams = [];

    $beams[] = new Beam($grid, ...$config);
    while (!empty($beams)) {
        $beam = array_pop($beams);
        do {
            $result = $beam->move();

            if (!$result) {
                break;
            }

            if (strlen($result) === 2) {
                [$result1, $result2] = str_split($result);
                $beams[] = new Beam($grid, $beam->row, $beam->col, $result1);
                $beam->direction = $result2;
            } else if (strlen($result) === 1) {
                $beam->direction = $result;
            }
        } while (true);
    }
    $max = max($max, count($grid->energized));

    echo sprintf("%d of %d traversed, max is %d\n", $idx, $configCount, $max);
}

echo $max;