<?php
declare (strict_types = 1);

namespace Async\Stream\Pipe;

use Async\Promise\Awaitable;
use Async\Promise\Promise;
use Async\Stream\ReadableStream;

final class ReadablePipe implements ReadableStream
{
    /**
     * @var resource
     */
    private $handler;

    /**
     * @var string
     */
    private $buffer = '';

    /**
     * @var int
     */
    private $bufferLength = 0;

    private $pendingPromise = null;

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

    public function read(int $maxLength = 0) : Awaitable
    {
        $promise = new Promise();

        uv_read_start($this->handler, function ($data) use ($promise) {
            $promise->resolve($data);

            uv_read_stop($this->handler);
        });

        return $promise;
    }

    public function isReadable(): bool
    {
        return isset($this->handler);
    }
}
