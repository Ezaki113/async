<?php
declare (strict_types = 1);

namespace Async\Socket;

use Async\Promise\Awaitable;
use Async\Promise\Promise;
use Async\Promise\RejectedPromise;
use Async\Stream\DuplexStream;

final class Socket implements DuplexStream
{
    private const CHUNK_LENGTH = 32;

    /**
     * @var
     */
    private $handler;

    /**
     * @var string
     */
    private $buffer = '';

    /**
     * @var array
     */
    private $queue;

    /**
     * @var bool
     */
    private $writable = true;

    /**
     * @var bool
     */
    private $readable = true;

    public function __construct($handler)
    {
        $this->handler = $handler;

        uv_read_start($this->handler, function($socket, $nread, $buffer) {
            if ($nread < 0) {
                $this->close();

                return;
            }

            $this->buffer .= $buffer;

            $this->checkPendingPromises();

            $this->close();
        });
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function close(): void
    {
        $this->writable = false;
        $this->readable = false;

        if ($this->handler) {
            uv_close($this->handler);
            uv_read_stop($this->handler);
        }

        if (empty($this->queue)) {
            return;
        }

        $exception = new \Exception('The stream was unexpectedly closed');

        while ($task = array_shift($this->queue)) {
            /** @var Promise $promise */
            $promise = $task[1];

            $promise->reject($exception);
        }
    }

    public function read(int $maxLength = self::CHUNK_LENGTH): Awaitable
    {
        if ($maxLength <= 0) {
            throw new \InvalidArgumentException('Length must be greater than 0');
        }

        if (!$this->readable) {
            return new RejectedPromise(new \LogicException('The socket is not readable.'));
        }

        $promise = new Promise();
        $this->queue[] = [$maxLength, $promise];

        $this->checkPendingPromises();

        return $promise;
    }

    public function isOpen(): bool
    {
        return isset($this->handler);
    }

    /**
     * @param string $data
     * @return Awaitable <int> number of bytes written
     *
     */
    public function write(string $data): Awaitable
    {
        if (!$this->writable) {
            return new RejectedPromise(new \LogicException('The socket is not writable.'));
        }

        return $this->send($data);
    }

    /**
     * @param string $data
     * @return Awaitable <int> number of bytes written
     */
    public function end(string $data = '') : Awaitable
    {
        if (!$this->writable) {
            return new RejectedPromise(new \LogicException('The socket is not writable.'));
        }

        $this->writable = false;

        return $this->send($data);
    }

    private function checkPendingPromises()
    {
        while ($this->queue && $this->buffer) {
            /**
             * @var int $maxLength
             * @var Promise $promise
             */
            list(0 => $maxLength, 1 => $promise) = array_shift($this->queue);

            $chunk = substr($this->buffer, 0, $maxLength);
            $this->buffer = substr($this->buffer, $maxLength);

            $promise->resolve($chunk);
        }
    }

    /**
     * @param string $data
     * @return Awaitable
     */
    private function send(string $data) : Awaitable
    {
        $handler = $this->handler;
        $length = strlen($data);

        $promise = new Promise();
        uv_write($handler, $data, static function ($handler, int $status) use ($length, $promise) {
            if ($status < 0) {
                $promise->reject(new \Exception('uv_write status ' . $status));
                $this->close();
                return;
            }

            $promise->resolve($length);
        });

        return $promise;
    }
}
