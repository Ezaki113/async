<?php
declare (strict_types = 1);

namespace Async\Stream;

use Async\Promise\Awaitable;

interface SeekableStream extends Stream
{
    /**
     * @param int $offset
     * @param int $whence
     *  SEEK_SET - Set position equal to offset bytes.
     *  SEEK_CUR - Set position to current location plus offset.
     *  SEEK_END - Set position to end-of-file plus offset.
     * @param float $timeout
     * @return Awaitable<int> new pointer position
     */
    public function seek(int $offset, int $whence = SEEK_SET, float $timeout = 0.0) : Awaitable;

    /**
     * @return int current position
     */
    public function tell() : int;

    public function getLength() : ?int;
}
