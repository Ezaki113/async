<?php
declare (strict_types = 1);

namespace Async\Loop;

final class Timer
{
    /**
     * @var TimerManager
     */
    private $manager;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $period;

    /**
     * @var callable
     */
    private $closure;

    public function __construct(TimerManager $manager, int $timeout, int $period, callable $closure)
    {
        $this->manager = $manager;
        $this->timeout = $timeout;
        $this->period = $period;
        $this->closure = $closure;
    }

    /**
     * @return int
     */
    public function getTimeout() : int
    {
        return $this->timeout;
    }

    /**
     * @return int
     */
    public function getPeriod() : int
    {
        return $this->period;
    }

    public function __invoke()
    {
        return ($this->closure)($this);
    }

    public function start()
    {
        $this->manager->start($this);
    }

    public function stop()
    {
        $this->manager->stop($this);
    }

    public function isPending() : bool
    {
        return $this->manager->isPending($this);
    }
}
