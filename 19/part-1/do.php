<?php
$file = file('../in/input.txt');

class Flow {
    private array $rules = [];
    public string $name;

    public function __construct(string $flow, private array &$flows)
    {
        preg_match('/(\w+)\{(.*)\}/', $flow, $matches);
        [,$name, $rules] = $matches;
        $this->name = $name;

        $rules = explode(',', $rules);

        foreach ($rules as $rule) {
            preg_match('/(\w)([><])(\d+):(\w+)|([\w]+)/', $rule, $matches);
            array_shift($matches);
            $this->rules[] = $matches;
        }
    }

    public function route(Part $part)
    {
        foreach ($this->rules as $rule) {
            if (!empty($rule[4])) {
                return $this->pass($rule[4], $part);
            } elseif ($this->match($rule, $part)) {
                return $this->pass($rule[3], $part);
            }
        }
    }

    public function match($rule, Part $part)
    {
        $prop = $part->{$rule[0]};
        if ($rule[1] === '>') {
            return $prop > $rule[2];
        }
        return $prop < $rule[2];
    }

    public function pass(string $route, Part $part)
    {
        if ($route === 'A') {
            return $part->x + $part->m + $part->a + $part->s;
        }
        if ($route === 'R') {
            return 0;
        }
        return $this->flows[$route]->route($part);
    }
}

class Part {
    public function __construct(public int $x, public int $m, public int $a, public int $s)
    {

    }
}

$flows = [];
while (true) {
    $line = trim(array_shift($file));

    if (empty($line)) {
        break;
    }

    $flow = new Flow($line, $flows);
    $flows[$flow->name] = $flow;
}

$sum = 0;

while (true) {
    if (empty($file)) {
        break;
    }
    $line = trim(array_shift($file));
    preg_match('/\{x=(\d+),m=(\d+),a=(\d+),s=(\d+)\}/', $line, $matches);
    array_shift($matches);
    $part = new Part(...$matches);
    /** @var Flow[] $flows */
    $sum += $flows['in']->route($part);
}

echo $sum;
