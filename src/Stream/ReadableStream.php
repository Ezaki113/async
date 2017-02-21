<?php
declare (strict_types = 1);

namespace Async\Stream;

use Async\Promise\Awaitable;

interface ReadableStream extends Stream
{
    public function read(int $maxLength = 0) : Awaitable;

    public function isReadable() : bool;
}
