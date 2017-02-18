<?php
declare (strict_types = 1);

namespace Async\Loop;

interface TimerManager
{
    public function start(Timer $timer) : void;

    public function stop(Timer $timer) : void;

    public function isPending(Timer $timer) : bool;
}
