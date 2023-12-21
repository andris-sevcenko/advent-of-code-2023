<?php
$file = file('../in/input.txt');

abstract class Module
{
    public function __construct(public string $name, protected PulseManager $manager, public array $outputs)
    {
        $this->manager->registerReverseConnections($this->name, $outputs);
    }

    abstract public function high(string $source);
    abstract public function low(string $source);

    protected function send(bool $signal) {
        foreach ($this->outputs as $module) {
            $this->manager->queuePulse($signal, $module, $this->name);
        }
    }
}

class FlipFlop extends Module
{
    private $state = false;

    public function low(string $source)
    {
        $this->state = !$this->state;
        $this->send($this->state);
    }

    public function high(string $source)
    {
        // noop
    }
}

class Conjunction extends Module
{
    public array $states = [];

    public function registerInputs(array $inputs) {
        foreach ($inputs as $input) {
            $this->states[$input] = false;
        }
    }

    public function low(string $source)
    {
        $this->states[$source] = false;
        $this->sendPulse();
    }

    public function high(string $source)
    {
        if ($this->name === 'zr') {
            global $i;
            echo strtoupper($source) . ' sends HIGH on ' . $i . "\n";
        }
        $this->states[$source] = true;
        $this->sendPulse();
    }

    protected function sendPulse()
    {
        $signal = false;
        foreach ($this->states as $state) {
            if (!$state) {
                $signal = true;
                break;
            }
        }
        $this->send($signal);
    }
}

class Broadcaster extends Module
{
    public function low(string $source)
    {
        $this->send(false);
    }

    public function high(string $source)
    {
        $this->send(true);
    }

}

class TestingModule extends Module
{
    public function low(string $source)
    {
        // noop
    }

    public function high(string $source)
    {
        // noop
    }
}
class PulseManager
{
    private array $queue = [];

    public array $reverseConnections = [];
    public int $highSent = 0;
    public int $lowSent = 0;
    public int $rx = 0;

    /** @var Module[] */
    public array $modules = [];

    public function registerModule(Module $module)
    {
        $this->modules[$module->name] = $module;
    }

    public function queuePulse(bool $signal, string $destination, string $source)
    {
        $this->queue[] = [$source, $destination, $signal];
    }

    public function tick()
    {
        [$source, $dest, $signal] = array_shift($this->queue);
        $module = $this->modules[$dest] ?? new TestingModule($dest, $this, []);

        if ($signal) {
            $module->high($source);
            $this->lowSent++;
        } else {
            $module->low($source);
            $this->highSent++;
        }

        return count($this->queue) > 0;
    }

    public function registerReverseConnections(string $moduleName, array $outputs)
    {
        foreach ($outputs as $output) {
            if (empty($this->reverseConnections[$output])) {
                $this->reverseConnections[$output] = [];
            }
            $this->reverseConnections[$output][] = $moduleName;
        }
    }

    public function prepareConjunctions()
    {
        $sources = [];
        foreach ($this->modules as $module)
        {
            if ($module instanceof Conjunction) {
                $module->registerInputs($this->reverseConnections[$module->name] ?? []);
            }
        }
    }
}

$manager = new PulseManager();
$permutations = 1;

while (true) {
    if (empty($file)) {
        break;
    }

    $line = trim(array_shift($file));
    preg_match('/(%?&?\w+) -> (\w+.*)$/', $line, $matches);
    [,$moduleName, $destinations] = $matches;
    $destinations = explode(',', $destinations);

    foreach ($destinations as &$dest) {
        $dest = trim($dest);
    }
    unset($dest);

    if (str_starts_with($moduleName, '&')) {
        $module = new Conjunction(substr($moduleName, 1), $manager, $destinations);
    } else if (str_starts_with($moduleName, '%')) {
        $module = new FlipFlop(substr($moduleName, 1), $manager, $destinations);
    } else {
        $module = new Broadcaster($moduleName, $manager, $destinations);
    }

    $manager->registerModule($module);
}

$manager->prepareConjunctions();
for ($i = 0; true; $i++) {
    $manager->queuePulse(false, 'broadcaster', 'button');
    while ($manager->tick()) {
        // noop
    }
}
