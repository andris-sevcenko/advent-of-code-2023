<?php
$file = file('../in/input.txt');

abstract class Module
{
    public function __construct(public string $name, protected PulseManager $manager, public array $outputs)
    {

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

    public int $highSent = 0;
    public int $lowSent = 0;

    /** @var Module[] */
    private array $modules = [];

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

    public function prepareConjunctions()
    {
        $conjunctions = [];
        $sources = [];
        foreach ($this->modules as $module)
        {
            if ($module instanceof Conjunction) {
                $conjunctions[$module->name] = $module;
            }
            foreach ($module->outputs as $output) {
                if (empty($sources[$output])) {
                    $sources[$output] = [];
                }
                $sources[$output][] = $module->name;
            }
        }
        /** @var Conjunction $conjunction */
        foreach ($conjunctions as $conjunction) {
            $conjunction->registerInputs($sources[$conjunction->name] ?? []);
        }
    }
}

$manager = new PulseManager();

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
for ($i = 0; $i < 1000; $i++) {
    $manager->queuePulse(false, 'broadcaster', 'button');
    while ($manager->tick()) {
        // noop
    }
}
echo $manager->lowSent * $manager->highSent;
echo "\n";
