<?php
declare (strict_types = 1);

namespace Async\Http\Parser;

use Async\Promise\Awaitable;
use Async\Socket\Socket;

interface Parser
{
    public function parseRequest(Socket $socket) : Awaitable;
}
