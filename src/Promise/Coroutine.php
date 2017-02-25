<?php
declare (strict_types = 1);

namespace Async\Promise;

use function Async\exception_for;
use Throwable;

final class Coroutine implements Awaitable
{
    /**
     * @var \Generator
     */
    private $generator;

    /**
     * @var Awaitable
     */
    private $currentPromise;

    /**
     * @var Awaitable
     */
    private $result;

    public function __construct(callable $generatorFn)
    {
        $this->generator = $generatorFn();
        $this->result = new Promise();
        $this->next($this->generator->current());
    }

    public function then(?callable $onFulfilled, ?callable $onRejected = null): Awaitable
    {
        return $this->result->then($onFulfilled, $onRejected);
    }

    public function otherwise(?callable $onRejected): Awaitable
    {
        return $this->result->otherwise($onRejected);
    }

    public function resolve($value): void
    {
        $this->result->resolve($value);
    }

    public function reject($reason): void
    {
        $this->result->reject($reason);
    }

    public function isPending(): bool
    {
        return $this->result->isPending();
    }

    public function isFulfilled(): bool
    {
        return $this->result->isFulfilled();
    }

    public function isRejected(): bool
    {
        return $this->result->isRejected();
    }

    private function next($yielded)
    {
        $this->currentPromise = \Async\promise_for($yielded)
            ->then(
                function ($value) {
                    unset($this->currentPromise);
                    try {
                        $next = $this->generator->send($value);
                        if ($this->generator->valid()) {
                            $this->next($next);
                        } else {
                            $this->result->resolve($value);
                        }
                    } catch (Throwable $throwable) {
                        $this->result->reject($throwable);
                    }
                },
                function ($reason) {
                    unset($this->currentPromise);
                    try {
                        $nextYield = $this->generator->throw(exception_for($reason));
                        $this->next($nextYield);
                    } catch (Throwable $throwable) {
                        $this->result->reject($throwable);
                    }
                }
            );
    }

}
