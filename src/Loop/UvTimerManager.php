<?php
declare (strict_types = 1);

namespace Async\Loop;

use Async\Loop\Timer;

final class UvTimerManager implements TimerManager
{
    /**
     * @var resource[]
     */
    private $handlers = [];

    /**
     * @var resource
     */
    private $loop;

    public function __construct($loop)
    {
        $this->loop = $loop;
    }

    public function start(Timer $timer) : void
    {
        $hash = spl_object_hash($timer);

        if (isset($this->handlers[$hash])) {
            return;
        }

        $timerHandler = uv_timer_init($this->loop);
        $this->handlers[$hash] = $timerHandler;

        uv_timer_start(
            $timerHandler,
            $timer->getTimeout(),
            $timer->getPeriod(),
            $timer
        );
    }

    public function stop(Timer $timer) : void
    {
        $hash = spl_object_hash($timer);

        if (!isset($this->handlers[$hash])) {
            return;
        }

        $handler = $this->handlers[$hash];

        \uv_timer_stop($handler);
    }

    public function isPending(Timer $timer): bool
    {
        $hash = spl_object_hash($timer);

        return isset($this->handlers[$hash]);
    }
}
