<?php
$file = file('../in/input.txt');

$directions = str_split(trim($file[0]));

array_shift($file);
array_shift($file);

$nodes = [];

// Create a traverser for each starting node
// Traverse one node at a time, tell ledger about node traversed
// As soon as the same node hash visited (step in the instruction chain + node id), bail out, as we are going in circles
// Get the target nodes from journey and traversal amount it takes
// Cross-compare, find out possible matches. [Ended up not needing this]
// Calculate at which point all the nodes would converge on a Z node

class Node
{
    public function __construct(public string $id, public string $l, public string $r)
    {
    }
}

class Ledger
{
    protected array $nodes;
    protected array $traversed;
    protected array $paths;

    public function addNode(Node $node): void
    {
        $this->nodes[$node->id] = $node;
    }

    public function getNode(string $nodeId): Node
    {
        return $this->nodes[$nodeId];
    }

    public function getAllNodes(): array
    {
        return $this->nodes;
    }

    public function mark(int $traverserId, string $nodeId, int $idx): bool
    {
        $nodeHash = $idx . $nodeId;

        if (!empty($this->traversed[$traverserId][$nodeHash])) {
            return false;
        }

        $this->traversed[$traverserId][$nodeHash] = true;
        $this->paths[$traverserId][] = $nodeId;

        return true;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }
}

class Traverser
{
    protected $isActive = true;
    protected $loop;
    protected $idx = 0;

    public function __construct(protected int $id, public Node $node, protected Ledger $ledger, protected array $directions)
    {
        $this->loop = count($directions) - 1;
    }

    public function move(): void
    {
        if ($this->directions[$this->idx] === 'L') {
            $this->l();
        } else {
            $this->r();
        }

        $this->isActive = $this->ledger->mark($this->id, $this->node->id, $this->idx);
        $this->idx = $this->idx === $this->loop ? 0 : $this->idx + 1;
    }

    public function l(): void
    {
        $this->node = $this->ledger->getNode($this->node->l);
    }

    public function r(): void
    {
        $this->node = $this->ledger->getNode($this->node->r);
    }

    public function isActive()
    {
        return $this->isActive;
    }
}

$ledger = new Ledger();

foreach ($file as $line) {
    preg_match('/^(\w+).*\((\w+), (\w+)\)/', $line, $matches);
    $ledger->addNode(new Node($matches[1], $matches[2], $matches[3]));
}

$traversers = [];
$trId = 0;

foreach ($ledger->getAllNodes() as $node) {
    if (str_ends_with($node->id, 'A')) {
        $traversers[] = new Traverser(++$trId, $node, $ledger, $directions);
    }
}

// Move all traversers, until all of them are going in circles
while (true) {
    $moved = false;
    foreach ($traversers as $traverser) {
        if ($traverser->isActive()) {
            $traverser->move();
            $moved = true;
        }
    }
    if (!$moved) {
        break;
    }
}

$targets = [];
foreach ($ledger->getPaths() as $traverser => $path) {
    foreach ($path as $idx =>$step) {
        if (str_ends_with($step, 'Z')) {
            // Just one Z node for each, boring
            $targets[$traverser] = $idx;
        }
    }
}

$stepNumber = [];
foreach ($targets as $target) {
    $stepNumber[] = $target + 1;
}

print_r($stepNumber);
