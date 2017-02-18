<?php
declare (strict_types = 1);

namespace Async\Promise;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

class FulfilledPromiseTest extends TestCase
{
    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function construct_Awaitable_ThrowException()
    {
        $reason = $this->createMock(Awaitable::class);

        new FulfilledPromise($reason);
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function resolve_Any_ThrowException()
    {
        $promise = new FulfilledPromise('some value');

        $promise->resolve('another value');
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function reject_Any_ThrowException()
    {
        $promise = new FulfilledPromise('some value');

        $promise->reject('another value');
    }

    /**
     * @test
     */
    public function then_Null_Self()
    {
        $promise = new FulfilledPromise('some value');

        $this->assertSame(
            $promise,
            $promise->then(null)
        );
    }

    /**
     * @test
     */
    public function then_Callback_ResolveWithValue()
    {
        $promise1 = new FulfilledPromise($expectedValue = 'some value');

        $promise2 = $promise1->then(function ($value) use ($expectedValue) {
            $this->assertEquals($expectedValue, $value);
        });

        $this->assertTrue($promise2->isFulfilled());
    }

    /**
     * @test
     */
    public function then_CallbackError_RejectWithErrorAsReason()
    {
        $promise1 = new FulfilledPromise('some value');

        $promise2 = $promise1->then(function () {
            throw new LogicException;
        });

        $this->assertTrue($promise2->isRejected());
    }
}
