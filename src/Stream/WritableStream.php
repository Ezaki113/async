<?php
declare (strict_types = 1);

namespace Async\Stream;

use Async\Promise\Awaitable;

interface WritableStream extends Stream
{
    public function isWritable() : bool;

    /**
     * @param string $data
     * @param float $timeout
     *
     * @return Awaitable<int> number of bytes written
     */
    public function write(string $data, float $timeout = 0.0) : Awaitable;

    /**
     * @param string $data
     * @param float $timeout
     * @return Awaitable<int> number of bytes written
     */
    public function end(string $data = '', float $timeout = 0.0) : Awaitable;
}
