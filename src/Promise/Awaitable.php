<?php
declare (strict_types = 1);

namespace Async\Promise;

interface Awaitable
{
    public function then(?callable $onFulfilled, ?callable $onRejected = null) : Awaitable;

    public function otherwise(?callable $onRejected) : Awaitable;

    public function resolve($value) : void;

    public function reject($reason) : void;

    public function isPending() : bool;

    public function isFulfilled() : bool;

    public function isRejected() : bool;
}
