<?php
declare (strict_types = 1);

namespace Async\Promise;

use LogicException;
use PHPUnit\Framework\TestCase;

class PromiseTest extends TestCase
{
    /**
     * @test
     */
    public function construct_Any_IsPending()
    {
        $promise = new Promise();

        $this->assertTrue($promise->isPending());
    }

    /**
     * @test
     */
    public function resolve_IsPending_IsFulfilled()
    {
        $promise = new Promise();

        $promise->resolve('value');

        $this->assertTrue($promise->isFulfilled());
    }

    /**
     * @test
     */
    public function reject_IsPending_IsRejected()
    {
        $promise = new Promise();

        $promise->reject('reason');

        $this->assertTrue($promise->isRejected());
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function resolve_IsNotPending_LogicException()
    {
        $promise = new Promise();
        $promise->resolve('value');

        $promise->resolve('another value');
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function reject_IsNotPending_LogicException()
    {
        $promise = new Promise();
        $promise->resolve('value');

        $promise->reject('reason');
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function resolve_Self_LogicException()
    {
        $promise = new Promise();

        $promise->resolve($promise);
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function reject_Self_LogicException()
    {
        $promise = new Promise();

        $promise->reject($promise);
    }

    /**
     * @test
     */
    public function resolve_PromiseWithPendingStateWithDownstreamPromise_DownstreamPromiseResolved()
    {
        $carry = null;
        $promise1 = new Promise();
        $promise2 = $promise1->then(function ($value) use (&$carry) {
            $carry = $value;
        });

        $promise1->resolve('value');

        $this->assertEquals('value', $carry);
    }

    /**
     * @test
     */
    public function then_PromiseWithResolvedState_DownstreamOnFulfilledCalled()
    {
        $promise1 = new Promise();
        $promise1->resolve('value');
        $carry = null;

        $promise1->then(function ($value) use (&$carry) {
            $carry = $value;
        });

        $this->assertSame('value', $carry);
    }

    /**
     * @test
     */
    public function then_PromiseWithResolvedState_FulfilledPromise()
    {
        $promise = new Promise();
        $promise->resolve('value');

        $promise2 = $promise->then(null);

        $this->assertInstanceOf(FulfilledPromise::class, $promise2);
    }

    /**
     * @test
     */
    public function reject_PromiseWithPendingStateWithDownstreamPromise_DownstreamPromiseRejected()
    {
        $carry = null;
        $promise1 = new Promise();
        $promise2 = $promise1->then(
            null,
            function ($value) use (&$carry) { $carry = $value; }
        );

        $promise1->reject('reason');

        $this->assertEquals('reason', $carry);
    }

    /**
     * @test
     */
    public function then_PromiseWithRejectedState_DownstreamPromiseOnRejectedCalled()
    {
        $carry = null;
        $promise1 = new Promise();
        $promise1->reject('reason');

        $promise1->then(
            null,
            function ($value) use (&$carry) { $carry = $value; }
        );

        $this->assertEquals('reason', $carry);
    }

    /**
     * @test
     */
    public function then_PromiseWithRejectedState_RejectedPromise()
    {
        $promise1 = new Promise();
        $promise1->reject('reason');

        $promise2 = $promise1->then(null, null);

        $this->assertInstanceOf(RejectedPromise::class, $promise2);
    }

    /**
     * @test testForwardsFulfilledDownChainBetweenGaps
     */
    public function resolve_MultipleDownstreamPromises()
    {
        $promise = new Promise();
        $carry1 = $carry2 = null;
        $promise
            ->then(null, null)
            ->then(function ($value) use (&$carry1) { $carry1 = $value; return $value . ' with appendix'; })
            ->then(function ($value) use (&$carry2) { $carry2 = $value; });

        $promise->resolve('value');

        $this->assertEquals('value', $carry1);
        $this->assertEquals('value with appendix', $carry2);
    }

    /**
     * @test testForwardsFulfilledDownChainBetweenGaps
     */
    public function reject_RejectMultipleDownstreamPromises()
    {
        $promise = new Promise();
        $carry1 = $carry2 = null;
        $promise
            ->then(null, null)
            ->then(null, function ($value) use (&$carry1) { $carry1 = $value; return $value . ' with appendix'; })
            ->then(function ($value) use (&$carry2) { $carry2 = $value; });

        $promise->reject('reason');

        $this->assertEquals('reason', $carry1);
        $this->assertEquals('reason with appendix', $carry2);
    }

    /**
     * @test
     */
    public function reject_ThrowMultipleDownstreamPromises()
    {
        $exception = new \Exception();
        $promise = new Promise();
        $carry1 = $carry2 = null;
        $promise
            ->then(null, null)
            ->then(null, function ($value) use (&$carry1, $exception) {
                $carry1 = $value;

                throw $exception;
            })->then(null, function ($value) use (&$carry2) {
                $carry2 = $value;
            });

        $promise->reject('reason');

        $this->assertEquals('reason', $carry1);
        $this->assertSame($exception, $carry2);
    }

    /**
     * @test
     */
    public function reject_ReturnedRejectedPromiseMultipleDownstreamPromises()
    {
        $promise = new Promise();
        $rejectedPromise = new RejectedPromise('reason 1');
        $carry1 = $carry2 = null;
        $promise
            ->then(null, null)
            ->then(null, function ($value) use (&$carry1, $rejectedPromise) {
                $carry1 = $value;

                return $rejectedPromise;
            })->then(null, function ($value) use (&$carry2) {
                $carry2 = $value;
            });

        $promise->reject('reason 2');

        $this->assertEquals('reason 2', $carry1);
        $this->assertEquals('reason 1', $carry2);
    }


    /**
     * @test
     */
    public function resolve_ResolvedPromiseReturnsPromiseToDownstream()
    {
        $carry = null;
        $promise1 = new Promise();
        $promise1->resolve('resolve 1');
        $promise2 = new Promise();
        $promise1->then(function ($value) use ($promise2) {
                return $promise2;
            })->then(function ($value) use (&$carry) {
                $carry = $value;
            });

        $promise2->resolve('resolve 2');

        $this->assertEquals('resolve 2', $carry);
    }
}
