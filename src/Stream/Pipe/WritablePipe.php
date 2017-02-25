<?php
declare (strict_types = 1);

namespace Async\Stream\Pipe;

use Async\Promise\Awaitable;
use Async\Promise\Promise;
use Async\Stream\WritableStream;

final class WritablePipe implements WritableStream
{
    /**
     * @var resource
     */
    private $handler;

    /**
     * @var bool
     */
    private $writable = true;

    /**
     * @param resource $loop
     * @param resource $resource
     */
    public function __construct($loop, $resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Invalid resource');
        }

        $this->handler = uv_pipe_init($loop, false);
        uv_pipe_open($this->handler, (int) $resource);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $data): Awaitable
    {
        $handler = $this->handler;
        $length = strlen($data);

        $promise = new Promise();
        uv_write($handler, $data, static function ($handler, int $status) use ($length, $promise) {
            if ($status < 0) {
                $this->close();
                $promise->reject(new \Exception('uv_write status ' . $status));

                return;
            }

            $promise->resolve($length);
        });

        return $promise;
    }

    public function end(string $data = ''): Awaitable
    {
        return $this->write($data)->then(function () {
            $this->close();
        });
    }

    public function isOpen(): bool
    {
        return isset($this->handler);
    }

    public function close(): void
    {
        if (is_resource($this->handler)) {
            uv_close($this->handler);

            $this->handler = null;
        }
    }
}
