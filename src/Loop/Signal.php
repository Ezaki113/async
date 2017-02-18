<?php
declare (strict_types = 1);

namespace Async\Loop;

final class Signal
{
    /**
     * @var SignalManager
     */
    private $manager;

    /**
     * @var int
     */
    private $signo;

    /**
     * @var callable
     */
    private $callback;

    public function __construct(SignalManager $manager, int $signo, callable $callback)
    {
        $this->manager = $manager;
        $this->signo = $signo;
        $this->callback = $callback;
    }

    public function __invoke()
    {
        return ($this->callback)($this);
    }

    public function enable() : void
    {
        $this->manager->enable($this);
    }

    public function disable() : void
    {
        $this->manager->disable($this);
    }

    public function isEnabled() : bool
    {
        return $this->manager->isEnabled($this);
    }

    /**
     * @return int
     */
    public function getSigno() : int
    {
        return $this->signo;
    }
}
