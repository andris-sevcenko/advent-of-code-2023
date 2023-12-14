<?php
$file = file('../in/input.txt');

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
}
class Rock
{
    public function __construct(protected Grid $grid, public $row, public $col){

    }

    public function move() {
        while ($this->row !== 0 && $this->grid->isEmpty($this->row -1, $this->col)) {
            $this->grid->setCell($this->row - 1, $this->col, 'O');
            $this->grid->setCell($this->row, $this->col, '.');
            $this->row--;
        }
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
foreach ($rocks as $rock) {
    $rock->move();
    $sum += 100 - $rock->row;
}

echo $sum;
