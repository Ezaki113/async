<?php
declare (strict_types = 1);

namespace Async\Promise;

final class FulfilledPromise implements Awaitable
{
    /**
     * @var
     */
    private $value;

    public function __construct($value)
    {
        if ($value instanceof Awaitable) {
            throw new \InvalidArgumentException('Value cannot be promise');
        }

        $this->value = $value;
    }

    public function then(?callable $onFulfilled, ?callable $onRejected = null): Awaitable
    {
        if ($onFulfilled === null) {
            return $this;
        }

        $value = $this->value;
        $promise = new Promise();

        $callback = static function() use ($promise, $value, $onFulfilled) {
            try {
                $promise->resolve($onFulfilled($value));
            } catch (\Throwable $exception) {
                $promise->reject($exception);
            }
        };

        \Async\queue($callback);

        return $promise;
    }

    public function otherwise(?callable $onRejected) : Awaitable
    {
        return $this;
    }

    public function resolve($value): void
    {
        throw new \LogicException('Cannot resolve a fulfilled promise');
    }

    public function reject($reason): void
    {
        throw new \LogicException('Cannot reject a fulfilled promise');
    }

    public function isPending(): bool
    {
        return false;
    }

    public function isFulfilled(): bool
    {
        return true;
    }

    public function isRejected(): bool
    {
        return false;
    }
}
