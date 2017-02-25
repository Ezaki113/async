<?php
declare (strict_types = 1);

namespace Async\Socket\Server;

use Async\Promise\Awaitable;

interface Server
{
    public function bind(string $address, int $host);

    public function listen();
}
