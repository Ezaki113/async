<?php
declare (strict_types = 1);

namespace Async\Loop;

interface SignalManager
{
    public function enable(Signal $signal) : void;

    public function disable(Signal $signal) : void;

    public function isEnabled(Signal $signal) : bool;
}
