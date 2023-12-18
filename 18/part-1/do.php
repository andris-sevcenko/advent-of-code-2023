<?php
$file = file('../in/input.txt');

class Grid
{
    private array $grid = [];
    private array $paint = [];
    public int $excavated = 0;
    public array $fill = [];

    public int $maxRow = 0;
    public int $maxCol = 0;

    public function setCell($row, $col, $cell, $color)
    {
        $this->grid[$row][$col] = $cell;
        $this->paint[$row][$col] = $color;

        $this->maxRow = max($this->maxRow, $row);
        $this->maxCol = max($this->maxCol, $col);
    }

    public function getCell($row, $col)
    {
        return $this->grid[$row][$col];
    }

    public function printGrid()
    {
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $cell) {
                if (!empty($this->fill[$row][$col])) {
                    echo '#';
                } else {
                    echo $cell;
                }
            }
            echo "\n";
        }
    }

    public function normalize($offsetRow, $offsetCol)
    {
        $newGrid = [];
        $maxCol = 0;
        $maxRow = 0;
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $char) {
                $newCol = $col - $offsetCol;
                $newRow = $row - $offsetRow;
                $newGrid[$newRow][$newCol] = $char;
                $maxCol = max($maxCol, $newCol);
                $maxRow = max($maxRow, $newRow);
            }
        }

        $this->maxRow = $maxRow;
        $this->maxCol = $maxCol;

        $this->grid = $newGrid;

        for ($i = 0; $i <= $maxRow; $i++) {
            for ($ii = 0; $ii <= $maxCol; $ii++) {
                if (empty($this->grid[$i][$ii])) {
                    $this->grid[$i][$ii] = '.';
                }
            }
            ksort($this->grid[$i]);
        }
        ksort($this->grid);
    }

    public function trace()
    {
        echo 'Tracing..' . "\n";
        for ($i = 0; $i <= $this->maxRow; $i++) {
            for ($ii = 0; $ii <= $this->maxCol; $ii++) {
                if ($this->grid[$i][$ii] === '#') {
                    $this->excavated++;
                } else {
                    if ($this->isInside($i, $ii)) {
                        $this->excavated++;
                        $this->fill[$i][$ii] = true;
                    }
                }
            }
        }
    }

    public function isInside($row, $col)
    {
        $trackingCorner = null;
        $flips = 0;
        $uCorner = false;
        $dCorner = false;

        for ($i = $col; $i <= $this->maxCol; $i++) {
            $cell = $this->grid[$row][$i];
            $after = ($this->grid[$row][$i + 1] ?? null) === '#';
            $before = ($this->grid[$row][$i - 1] ?? null) === '#';
            $above = ($this->grid[$row - 1][$i] ?? null) === '#';
            $below = ($this->grid[$row + 1][$i] ?? null) === '#';

            $wall = $above && $below;
            $previousUCorner = $uCorner;
            $previousDCorner = $dCorner;
            $uCorner = $above && ($before xor $after);
            $dCorner = $below && ($before xor $after);

            if ($cell === '#') {
                if ($wall) {
                    $flips++;
                } else {
                    if (!$trackingCorner) {
                        $trackingCorner = $uCorner ? 'U' : 'D';
                    }
                }
            } else {
                if ($trackingCorner) {
                    if ($trackingCorner === 'U' && $previousDCorner) {
                        $flips++;
                    }
                    if ($trackingCorner === 'D' && $previousUCorner) {
                        $flips++;
                    }
                    $trackingCorner = null;
                }
            }
        }

        if ($trackingCorner) {
            if ($trackingCorner === 'U' && $dCorner) {
                $flips++;
            }
            if ($trackingCorner === 'D' && $uCorner) {
                $flips++;
            }
            $trackingCorner = null;
        }

        $inside = $flips % 2 === 1;
        return $inside ? 1 : 0;
    }

}

$grid = new Grid();

$pos = [0, 0];
$minRow = 0;
$minCol = 0;
foreach ($file as $line) {
    $line = trim($line);
    preg_match('/(\w) (\d+) \((#[a-f0-9]+)\)/', $line, $matches);
    [, $direction, $num, $color] = $matches;

    switch ($direction) {
        case 'U':
            $row = -1;
            $col = 0;
            break;
        case 'R':
            $row = 0;
            $col = 1;
            break;
        case 'D':
            $row = 1;
            $col = 0;
            break;
        case 'L':
            $row = 0;
            $col = -1;
            break;
    }
    for ($i = 0; $i < $num; $i++) {
        $pos = [$pos[0] + $row, $pos[1] + $col];
        $grid->setCell($pos[0], $pos[1], '#', $color);
    }
    $minRow = min($minRow, $pos[0]);
    $minCol = min($minCol, $pos[1]);
}

$grid->normalize($minRow, $minCol);
$grid->trace();
$grid->printGrid();
echo $grid->excavated;