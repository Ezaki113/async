<?php
declare (strict_types = 1);

namespace Async\Stream;

use Async\Promise\Awaitable;

interface WritableStream extends Stream
{
    public function isWritable() : bool;

    /**
     * @param string $data
     * @return Awaitable <int> number of bytes written
     *
     */
    public function write(string $data) : Awaitable;

    /**
     * @param string $data
     * @return Awaitable <int> number of bytes written
     */
    public function end(string $data = '') : Awaitable;
}
