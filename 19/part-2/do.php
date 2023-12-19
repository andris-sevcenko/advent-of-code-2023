<?php
$file = file('../in/input.txt');

/**
 * Create all paths going from A to `in`
 * For each flow in path, find the rule that would point to the path.
 * Grab all the preceding rules and make a note on the requirements to bypass them
 * Proceed until this list is created for all paths
 * For each path, create a resultset for each property (x,m,a,s)
 * Intersect each property resultsets
 * Multiply property resultset sizes together
 */
class Flow {
    private array $rules = [];
    public string $name;

    public function __construct(string $flow)
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

    public function reverseMatch(string $destination, bool $lastTerm = false) {
        $preceding = [];
        foreach ($this->rules as $rule) {
            if (!empty($rule[4]) && $rule[4] === $destination) {
                if ($lastTerm) {
                    return $preceding;
                }
                return [];
            }

            if ($rule[3] === $destination && !$lastTerm) {
                unset($rule[3]);
                $preceding[] = $rule;
                return $preceding;
            }

            if ($rule[1] === '>') {
                $rule[1] = '<=';
            } else {
                $rule[1] = '>=';
            }
            unset($rule[3]);

            $preceding[] = $rule;
        }
    }
}

/** @var Flow[] $flows */
$flows = [];
while (true) {
    $line = trim(array_shift($file));

    if (empty($line)) {
        break;
    }

    $flow = new Flow($line, $flows);
    $flows[$flow->name] = $flow;
}

$unfinishedReverseFlows = [];
$next = [];
$destination = 'A';

$flowIndex = 0;
foreach ($flows as $flow) {
    $explicitRules = $flow->reverseMatch($destination);

    if (!empty($explicitRules)) {
        $unfinishedReverseFlows[++$flowIndex] = $explicitRules;
        $next[$flowIndex] = $flow->name;
    }

    $catchAllRules = $flow->reverseMatch($destination, true);
    if (!empty($catchAllRules)) {
        $unfinishedReverseFlows[++$flowIndex] = $catchAllRules;
        $next[$flowIndex] = $flow->name;
    }
}
// $unfinishedReverseFlows = all the currently known reverse flows
// $next = using the same index as $unfinishedReverseFlows, look up the next flow name

$done = $unfinishedReverseFlows;
while (true) {
    $nextReverseFlows = [];
    $matched = false;

    foreach ($unfinishedReverseFlows as $nextFlow => $reverseFlow) {
        $temp = [];
        $destination = $next[$nextFlow];

        foreach ($flows as $flow) {
            // Find all ways on how to get to this flow
            $explicitRules = $flow->reverseMatch($destination);
            $catchAllRules = $flow->reverseMatch($destination, true);

            if (!empty($catchAllRules)) {
                $reverseFlow = array_merge($reverseFlow, $catchAllRules);
                if ($flow->name === 'in') {
                    $done[] = $reverseFlow;
                } else {
                    $nextReverseFlows[++$flowIndex] = $reverseFlow;
                    $next[$flowIndex] = $flow->name;
                    $matched = true;
                }
            }
            if (!empty($explicitRules) && empty($catchAllRules)) {
                $reverseFlow = array_merge($reverseFlow, $explicitRules);
                if ($flow->name === 'in') {
                    $done[] = $reverseFlow;
                } else {
                    $nextReverseFlows[++$flowIndex] = $reverseFlow;
                    $next[$flowIndex] = $flow->name;
                    $matched = true;
                }

            }
        }
    }
    $unfinishedReverseFlows = $nextReverseFlows;
    if (!$matched) {
        break;
    }
}
$total = 0;
foreach ($done as $restrictionSet) {
    $props = [];
    foreach ($restrictionSet as &$restriction) {
        switch ($restriction[1]) {
            case '>=':
                $props[$restriction[0]][] = range($restriction[2], 4000);
                break;
            case '>':
                $props[$restriction[0]][] = range($restriction[2] + 1, 4000);
                break;
            case '<=':
                $props[$restriction[0]][] = range(1, $restriction[2]);
                break;
            case '<':
                $props[$restriction[0]][] = range(1, $restriction[2] - 1);
                break;
        }
        $restriction = implode(' ', $restriction);
    }
    $subTotal = 1;
    foreach ($props as $char => $sets) {
        $subTotal *= count(array_intersect(...$sets));
    }

    if (count($props) === 3) {
        $subTotal *= 4000;
    }

    if (count($props) === 2) {
        $subTotal *= 4000 * 4000;
    }

    if (count($props) === 1) {
        $subTotal *= 4000 * 4000 * 4000;
    }

    $total += $subTotal;
}

echo $total;
