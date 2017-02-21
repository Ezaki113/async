<?php
declare (strict_types = 1);

namespace {
    /**
     * @see https://github.com/php/php-src/compare/master...kelunik:hrtime
     */
    if (!function_exists('hrtime')) {
        function hrtime(): float
        {
            return uv_hrtime() / 1000000000;
        }
    }
}

namespace Async {

    use Async\Loop\{
        Loop, UvLoop
    };
    use Async\Promise\{
        Awaitable, FulfilledPromise, RejectedPromise
    };

    function promise_for($value): Awaitable
    {
        if ($value instanceof Awaitable) {
            return $value;
        }

        return new FulfilledPromise($value);
    }

    function rejection_for($reason): Awaitable
    {
        if ($reason instanceof Awaitable) {
            return $reason;
        }

        return new RejectedPromise($reason);
    }

    function loop(): Loop
    {
        static $loop;

        if ($loop === null) {
            $loop = new UvLoop();
        }

        return $loop;
    }

    function queue(callable $callback): void
    {
        loop()->queue($callback);
    }
}

namespace Async\Stream {
    function stdout() : WritableStream {
        return \Async\loop()->stdout();
    };
}
