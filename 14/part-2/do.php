<?php
$file = file('../in/input.txt');
const GRID_LEN = 100;

class Grid
{
    private array $grid = [];

    public function setCell($row, $col, $cell)
    {
        $this->grid[$row][$col] = $cell;
    }

    public function isEmpty($row, $col) {
        return $this->grid[$row][$col] === '.';
    }

    public function hash() {
        return md5(var_export($this->grid, true));
    }

    public function printGrid() {
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $cell) {
                echo $cell;
            }
            echo "\n";
        }
    }
}
class Rock
{
    public function __construct(protected Grid $grid, public $row, public $col){

    }

    public function move($row, $col) {
        $moved = false;
        while (true) {
            $nextRow = $this->row + $row;
            $nextCol = $this->col + $col;

            if ($nextRow < 0 || $nextCol < 0 || $nextRow === GRID_LEN || $nextCol === GRID_LEN) {
                break;
            }

            if (!$this->grid->isEmpty($nextRow, $nextCol)) {
                break;
            }

            $moved = true;
            $this->grid->setCell($nextRow, $nextCol, 'O');
            $this->grid->setCell($this->row, $this->col, '.');

            $this->row = $nextRow;
            $this->col = $nextCol;
        }

        return $moved;
    }
}

$grid = new Grid();
$rocks = [];

foreach ($file as $row => $line) {
    $line = trim($line);
    foreach (str_split($line) as $col => $char) {
        $grid->setCell($row, $col, $char);
        if ($char === 'O') {
            $rocks[] = new Rock($grid, $row, $col);
        }
    }
}

$sum = 0;
$cycleAmount = 1000000000;

$hashes = [];
$hitCache = false;

for ($i = 0; $i < $cycleAmount; $i++) {
    while (true) {
        $moved = false;
        foreach ($rocks as $rock) {
            if ($rock->move(-1, 0)) {
                $moved = true;
            }
        }
        if (!$moved) {
            break;
        }
    }
    while (true) {
        $moved = false;
        foreach ($rocks as $rock) {
            if ($rock->move(0, -1)) {
                $moved = true;
            }
        }
        if (!$moved) {
            break;
        }
    }
    while (true) {
        $moved = false;
        foreach ($rocks as $rock) {
            if ($rock->move(1, 0)) {
                $moved = true;
            }
        }
        if (!$moved) {
            break;
        }
    }
    while (true) {
        $moved = false;
        foreach ($rocks as $rock) {
            if ($rock->move(0, 1)) {
                $moved = true;
            }
        }
        if (!$moved) {
            break;
        }
    }

    $hash = $grid->hash();
    echo $cycleAmount - $i ." - $hash \n";
    if (in_array($hash, $hashes) && !$hitCache) {
        $cycle = array_search($hash, $hashes);
        $len = $i - $cycle;
        $remainder = ($cycleAmount - $i) % $len;
        $hitCache = true;
        $i = $cycleAmount - $remainder;
    }

    $hashes[] = $hash;
}
foreach ($rocks as $rock) {
    $sum += GRID_LEN - $rock->row;
}

echo $sum;
