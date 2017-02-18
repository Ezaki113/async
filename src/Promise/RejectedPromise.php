<?php
declare (strict_types = 1);

namespace Async\Promise;

final class RejectedPromise implements Awaitable
{
    private $reason;

    public function __construct($reason)
    {
        if ($reason instanceof Awaitable) {
            throw new \InvalidArgumentException('Reason cannot be promise');
        }

        $this->reason = $reason;
    }

    public function then(?callable $onFulfilled, ?callable $onRejected = null): Awaitable
    {
        return $this->otherwise($onRejected);
    }

    public function otherwise(?callable $onRejected): Awaitable
    {
        if ($onRejected === null) {
            return $this;
        }

        $reason = $this->reason;
        $promise = new Promise();

        \Async\queue(
            static function () use ($promise, $reason, $onRejected) {
                try {
                    $promise->resolve($onRejected($reason));
                } catch (\Throwable $exception) {
                    $promise->reject($exception);
                }
            }
        );

        return $promise;
    }

    public function resolve($value): void
    {
        throw new \LogicException('Cannot resolve a rejected promise');
    }

    public function reject($reason): void
    {
        throw new \LogicException('Cannot reject a rejected promise');
    }

    public function isPending(): bool
    {
        return false;
    }

    public function isFulfilled(): bool
    {
        return false;
    }

    public function isRejected(): bool
    {
        return true;
    }
}
