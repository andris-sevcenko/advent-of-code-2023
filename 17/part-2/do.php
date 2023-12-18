<?php
$file = file('../in/input.txt');
ini_set('memory_limit', '1G');
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
            $len = strlen($direction);

            $visited = $this->visited[$move[1]][$move[2]][$direction][$len] ?? null;
            if (!$visited || $this->visited[$move[1]][$move[2]][$direction][$len] > $next) {
                $this->nodes[] = new Node($this->grid, $direction, $next, $move[1], $move[2], $this->nodes, $this->visited);
                $this->visited[$move[1]][$move[2]][$direction][$len] = $next;
                echo sprintf('Listing %d x %d at %d' . "\n", $move[1], $move[2], $next);
            }

            if ($move[1] === $this->grid->maxRow && $move[2] === $this->grid->maxCol && strlen($direction) > 3) {
                echo $this->val + $loss . "\n";
                die();
            }
        }
    }

    public function possibleMoves()
    {
        $keep = strlen($this->direction) < 4;
        $any = strlen($this->direction) >= 4 && strlen($this->direction) < 10;
        $turn = !$keep && !$any;
        $dir = $this->direction[0];

        return match (true) {
            $dir === 'C' => [
                ['R', $this->row, $this->col + 1],
                ['D', $this->row + 1, $this->col]
            ],

            in_array($dir, ['R', 'L'], true) && $turn => [
                ['U', $this->row - 1, $this->col],
                ['D', $this->row + 1, $this->col]
            ],
            in_array($dir, ['U', 'D'], true) && $turn => [
                ['R', $this->row, $this->col + 1],
                ['L', $this->row, $this->col - 1]
            ],

            $dir === 'R' && $keep => [
                ['R', $this->row, $this->col + 1],
            ],
            $dir === 'L' && $keep => [
                ['L', $this->row, $this->col - 1],
            ],
            $dir === 'U' && $keep => [
                ['U', $this->row - 1, $this->col],
            ],
            $dir === 'D' && $keep => [
                ['D', $this->row + 1, $this->col],
            ],

            $dir === 'R' && $any => [
                ['U', $this->row - 1, $this->col],
                ['R', $this->row, $this->col + 1],
                ['D', $this->row + 1, $this->col]
            ],
            $dir === 'L' && $any => [
                ['U', $this->row - 1, $this->col],
                ['L', $this->row, $this->col - 1],
                ['D', $this->row + 1, $this->col]
            ],
            $dir === 'U' && $any => [
                ['R', $this->row, $this->col + 1],
                ['L', $this->row, $this->col - 1],
                ['U', $this->row - 1, $this->col]
            ],
            $dir === 'D' && $any => [
                ['R', $this->row, $this->col + 1],
                ['L', $this->row, $this->col - 1],
                ['D', $this->row + 1, $this->col]
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


