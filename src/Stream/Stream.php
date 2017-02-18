<?php
declare (strict_types = 1);

namespace Async\Stream;

interface Stream
{
    public function isOpen() : bool;

    public function close() : void;
}
