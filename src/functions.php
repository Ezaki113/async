<?php
declare (strict_types = 1);

/**
 * @see https://github.com/php/php-src/compare/master...kelunik:hrtime
 */
use Async\Promise\Awaitable;
use Async\Promise\RejectedPromise;

if (!function_exists('hrtime')) {
    function hrtime() : float {
        return uv_hrtime() / 1000000000;
    }
}

function promise_for($value) : Awaitable {
    if ($value instanceof Awaitable) {
        return $value;
    }

    return new \Async\Promise\FulfilledPromise($value);
}

function rejection_for($reason) : Awaitable {
    if ($reason instanceof Awaitable) {
        return $reason;
    }

    return new RejectedPromise($reason);
}
