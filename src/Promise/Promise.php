<?php
declare (strict_types = 1);

namespace Async\Promise;

use function Async\promise_for;
use function Async\rejection_for;

final class Promise implements Awaitable
{
    private const STATE_PENDING = 0;
    private const STATE_FULFILLED = 1;
    private const STATE_REJECTED = 2;

    private $handlers = [];

    private $result;
    private $state = self::STATE_PENDING;

    public function then(?callable $onFulfilled, ?callable $onRejected = null): Awaitable
    {
        switch ($this->state) {
            case self::STATE_PENDING:
                $promise = new self();
                $this->handlers[] = [$promise, $onFulfilled, $onRejected];

                return $promise;

            case self::STATE_FULFILLED:
                $promise = promise_for($this->result);

                if ($onFulfilled) {
                    $promise = $promise->then($onFulfilled);
                }

                return $promise;

            case self::STATE_REJECTED:
                $promise = rejection_for($this->result);

                if ($onRejected) {
                    $promise = $promise->otherwise($onRejected);
                }

                return $promise;

            default:
                throw new \RuntimeException('Promise has unexpected state');
        }
    }

    public function otherwise(?callable $onRejected): Awaitable
    {
        return $this->then(null, $onRejected);
    }

    public function resolve($value): void
    {
        $this->settle(self::STATE_FULFILLED, $value);
    }

    public function reject($reason): void
    {
        $this->settle(self::STATE_REJECTED, $reason);
    }

    public function isPending(): bool
    {
        return $this->state === self::STATE_PENDING;
    }

    public function isFulfilled(): bool
    {
        return $this->state === self::STATE_FULFILLED;
    }

    public function isRejected(): bool
    {
        return $this->state === self::STATE_REJECTED;
    }

    private function settle(int $state, $value): void
    {
        if (!$this->isPending()) {
            throw new \LogicException('Cannot change resolved/rejected promise status');
        }

        if ($this === $value) {
            throw new \LogicException('Cannot fulfill or reject a promise with itself');
        }

        $this->state = $state;
        $this->result = $value;

        $handlers = $this->handlers;
        $this->handlers = [];

        if ($value instanceof Promise && $value->isPending()) {
            $value->handlers = array_merge($value->handlers, $handlers);

            return;
        } elseif ($value instanceof Awaitable) {
            $value->then(
                static function ($value) use ($handlers) {
                    foreach ($handlers as list(0 => $promise, 1 => $onFulfilled)) {
                        static::fulfillHandler($promise, $onFulfilled, $value);
                    }
                },
                static function ($reason) use ($handlers) {
                    foreach ($handlers as list(0 => $promise, 2 => $onRejected)) {
                        static::rejectHandler($promise, $onRejected, $reason);
                    }
                }
            );
        } elseif ($this->isFulfilled()) {
            \Async\queue(
                static function () use ($value, $handlers) {
                    foreach ($handlers as list(0 => $promise, 1 => $onFulfilled)) {
                        static::fulfillHandler($promise, $onFulfilled, $value);
                    }
                }
            );
        } elseif ($this->isRejected()) {
            \Async\queue(
                static function () use ($value, $handlers) {
                    foreach ($handlers as list(0 => $promise, 2 => $onRejected)) {
                        static::rejectHandler($promise, $onRejected, $value);
                    }
                }
            );
        }
    }

    private static function fulfillHandler(Awaitable $promise, ?callable $onFulfilled, $value)
    {
        if (!$promise->isPending()) {
            return;
        }

        try {
            if ($onFulfilled === null) {
                $promise->resolve($value);

                return;
            }

            $promise->resolve($onFulfilled($value));
        } catch (\Throwable $exception) {
            $promise->reject($exception);
        }
    }

    private static function rejectHandler(Awaitable $promise, ?callable $onRejected, $reason)
    {
        if (!$promise->isPending()) {
            return;
        }

        try {
            if ($onRejected === null) {
                $promise->reject($reason);

                return;
            }

            $promise->resolve($onRejected($reason));

        } catch (\Throwable $exception) {
            $promise->reject($exception);
        }
    }
}
