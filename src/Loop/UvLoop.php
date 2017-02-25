<?php
declare (strict_types = 1);

namespace Async\Loop;

use Async\Stream\Pipe\WritablePipe;
use Async\Stream\StdoutStream;
use Async\Stream\WritableStream;

final class UvLoop implements Loop
{
    /**
     * @var resource
     */
    private $loop;

    /**
     * @var TimerManager
     */
    private $timerManager;

    /**
     * @var SignalManager
     */
    private $signalManager;

    /**
     * @var bool
     */
    private $running = false;

    public function __construct()
    {
        $this->loop = \uv_loop_new();
        $this->timerManager = new UvTimerManager($this->loop);
        $this->signalManager = new UvSignalManager($this->loop);
    }

    public function __destruct()
    {
        if (is_resource($this->loop)) {
//             TODO after segfault fix
//            \uv_loop_delete($this->loop);

            $this->loop = null;
        }
    }

    /**
     * @return resource
     */
    public function getLoopHandler()
    {
        return $this->loop;
    }

    public function tick()
    {
        \uv_run($this->loop, \UV::RUN_ONCE);
    }

    public function run()
    {
        $this->running = true;

        \uv_run($this->loop, \UV::RUN_DEFAULT);
    }

    public function stop()
    {
        if (!is_resource($this->loop)) {
            return;
        }

        \uv_stop($this->loop);

        $this->running = false;
    }

    public function isRunning() : bool
    {
        return $this->running;
    }

    public function timer(int $timeout, int $period, callable $callback) : Timer
    {
        $timer = new Timer($this->timerManager, $timeout, $period, $callback);

        $this->timerManager->start($timer);

        return $timer;
    }

    public function signal(int $signo, callable $callback) : Signal
    {
        $signal = new Signal($this->signalManager, $signo, $callback);

        $this->signalManager->enable($signal);

        return $signal;
    }

    public function queue(callable $callback)
    {
        $callback();
//        uv_async_send(uv_async_init(
//            $this->loop,
//            static function ($handler) use ($callback) {
//                uv_close($handler);
//                $callback();
//            }
//        ));
    }

    public function stdout(): WritableStream
    {
        static $stdout;

        if ($stdout === null) {
            $stdout = new WritablePipe($this->loop, STDOUT);
        }

        return $stdout;
    }

    /**
     * @return resource
     */
    public function nativeHandler()
    {
        return $this->loop;
    }
}
