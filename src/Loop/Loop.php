<?php
declare (strict_types = 1);

namespace Async\Loop;

use Async\Stream\WritableStream;
use Closure;

interface Loop
{
    /**
     * @deprecated
     */
    public function tick();

    public function run();

    public function stop();

    public function isRunning() : bool;

    public function timer(int $timeout, int $period, callable $callback) : Timer;

    public function signal(int $signo, callable $callback) : Signal;

    public function queue(callable $callback);

    public function stdout() : WritableStream;
}
