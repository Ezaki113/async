<?php
declare (strict_types = 1);

namespace Async\Promise;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

class RejectedPromiseTest extends TestCase
{
    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function construct_Awaitable_ThrowException()
    {
        $reason = $this->createMock(Awaitable::class);

        new RejectedPromise($reason);
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function resolve_Any_ThrowException()
    {
        $promise = new RejectedPromise('some value');

        $promise->resolve('another value');
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function reject_Any_ThrowException()
    {
        $promise = new RejectedPromise('some value');

        $promise->reject('another value');
    }

    /**
     * @test
     */
    public function otherwise_Null_Self()
    {
        $promise = new RejectedPromise('some value');

        $this->assertSame(
            $promise,
            $promise->otherwise(null)
        );
    }

    /**
     * @test
     */
    public function otherwise_Callback_ResolveWithReason()
    {
        $promise1 = new RejectedPromise($reason = new \DomainException());

        $promise2 = $promise1->otherwise(function ($reason) {
            $this->assertInstanceOf(\DomainException::class, $reason);
        });

        $this->assertTrue($promise2->isFulfilled());
    }

    /**
     * @test
     */
    public function otherwise_CallbackError_RejectWithErrorAsReason()
    {
        $promise1 = new RejectedPromise('some reason');

        $promise2 = $promise1->then(function () {
            throw new LogicException;
        });

        $this->assertTrue($promise2->isRejected());
    }
}
