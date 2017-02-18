<?php
declare (strict_types = 1);

namespace Async\Loop;

final class UvSignalManager implements SignalManager
{
    /**
     * @var resource
     */
    private $loop;

    /**
     * @var resource[]
     */
    private $handlers = [];

    /**
     * @param resource $loop
     */
    public function __construct($loop)
    {
        $this->loop = $loop;
    }

    public function enable(Signal $signal): void
    {
        $hash = spl_object_hash($signal);

        if (isset($this->handlers[$hash])) {
            return;
        }

        $handler = uv_signal_init($this->loop);

        $this->handlers[$hash] = $handler;

        uv_signal_start($handler, $signal, $signal->getSigno());
    }

    public function disable(Signal $signal): void
    {
        $hash = spl_object_hash($signal);

        if (isset($this->handlers[$hash])) {
            return;
        }

        $handler = $this->handlers[$hash];

        uv_signal_stop($handler);
    }

    public function isEnabled(Signal $signal): bool
    {
        $hash = spl_object_hash($signal);

        return isset($this->handlers[$hash]);
    }
}
