<?php
declare (strict_types = 1);

namespace Async\Stream;

use Async\Promise\Awaitable;

interface ReadableStream extends Stream
{
    public function read(int $maxLength = 0, string $untilByte = null, float $timeout = 0.0) : Awaitable;

    public function isReadable() : bool;
}
