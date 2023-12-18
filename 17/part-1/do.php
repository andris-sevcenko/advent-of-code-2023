<?php
$file = file('../in/input.txt');

class Grid
{
    private array $grid = [];
    private array $visited = [];
    public int $maxRow = 0;
    public int $maxCol = 0;
    public function setCell($row, $col, $cell)
    {
        $this->grid[$row][$col] = $cell;

        $this->maxRow = max($this->maxRow, $row);
        $this->maxCol = max($this->maxCol, $col);
    }

    public function getCell($row, $col) {
        return $this->grid[$row][$col];
    }

    public function printGrid()
    {
        foreach ($this->grid as $row => $line) {
            foreach ($line as $col => $cell) {
                if (!empty($this->visited[$row][$col])) {
                    echo $this->visited[$row][$col];
                } else {
                    echo $cell;
                }
            }
            echo "\n";
        }
    }

    public function has($row, $col)
    {
        return array_key_exists($row, $this->grid) && array_key_exists($col, $this->grid[$row]);
    }
}

class Node
{
    public function __construct(public Grid $grid, public string $direction, public int $val, public int $row, public int $col, public array &$nodes, public array &$visited)
    {

    }

    public function move()
    {
        $moves = $this->possibleMoves();

        $newMoves = [];

        foreach ($moves as $move) {
            if ($this->grid->has($move[1], $move[2])) {
                $newMoves[] = $move;
            }
        }

        foreach ($newMoves as $move) {
            $loss = $this->grid->getCell($move[1], $move[2]);

            if (str_ends_with($this->direction, $move[0])) {
                $direction = $this->direction . $move[0];
            } else {
                $direction = $move[0];
            }

            $next = $this->val + $loss;

            $visited = $this->visited[$move[1]][$move[2]][$direction] ?? null;
            if (!$visited || $this->visited[$move[1]][$move[2]][$direction] > $next) {
                $this->nodes[] = new Node($this->grid, $direction, $next, $move[1], $move[2], $this->nodes, $this->visited);
                $this->visited[$move[1]][$move[2]][$direction] = $next;
                echo sprintf('Listing %d x %d at %d' . "\n", $move[1], $move[2], $next);
            }

            if ($move[1] === $this->grid->maxRow && $move[2] === $this->grid->maxCol) {
                $this->grid->printGrid();
                echo $this->val + $loss . "\n";
                die();
            }
        }
    }

    public function possibleMoves()
    {
        return match ($this->direction) {
            'C' => [
                ['R', $this->row, $this->col + 1],
                ['D', $this->row + 1, $this->col]
            ],
            'R', 'RR' => [
                ['U', $this->row - 1, $this->col],
                ['R', $this->row, $this->col + 1],
                ['D', $this->row + 1, $this->col]
            ],
            'RRR', 'LLL' => [
                ['U', $this->row - 1, $this->col],
                ['D', $this->row + 1, $this->col]
            ],
            'L', 'LL' => [
                ['U', $this->row - 1, $this->col],
                ['L', $this->row, $this->col - 1],
                ['D', $this->row + 1, $this->col]
            ],
            'U', 'UU' => [
                ['L', $this->row - 1, $this->col],
                ['U', $this->row, $this->col - 1],
                ['R', $this->row, $this->col + 1],
            ],
            'UUU', 'DDD' => [
                ['L', $this->row, $this->col - 1],
                ['R', $this->row, $this->col + 1],
            ],
            'D', 'DD' => [
                ['D', $this->row + 1, $this->col],
                ['L', $this->row, $this->col - 1],
                ['R', $this->row, $this->col + 1]
            ],
        };
    }
}

$grid = new Grid();

$nodes = [];
$visited = [];

foreach ($file as $row => $line) {
    $line = trim($line);
    foreach (str_split($line) as $col => $char) {
        $grid->setCell($row, $col, $char);
    }
}

$nodes[] = new Node($grid, 'C', 0, 0, 0, $nodes, $visited);
$counter = 0;
while (true) {
    $min = 999999999;
    $candidate = null;

    foreach ($nodes as $nodeId => $node) {
        if ($node->val < $min) {
            $min = $node->val;
            $candidate = $nodeId;
        }
    }

    $node = $nodes[$candidate];
    $node->move();
    unset ($nodes[$candidate]);
    echo sprintf('Number of nodes %d' . "\n", count($nodes));
}


