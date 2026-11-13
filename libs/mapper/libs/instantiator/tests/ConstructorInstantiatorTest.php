<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests;

use TypeLang\Mapper\Instantiator\Exception\ConstructionFailedException;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Instantiator\Tests\Stub\ChildStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\ConstructorStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EmptyStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\ThrowingStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\VariadicStub;

final class ConstructorInstantiatorTest extends TestCase
{
    public function testPassesPositionalArguments(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(ConstructorStub::class)
            ->instantiate([100, 'note', 'EUR']);

        self::assertTrue($object->isConstructed);
        self::assertSame(100, $object->amount);
        self::assertSame('note', $object->comment);
        self::assertSame('EUR', $object->currency);
    }

    public function testPassesNamedArguments(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(ConstructorStub::class)
            ->instantiate(['currency' => 'EUR', 'amount' => 100]);

        self::assertSame(100, $object->amount);
        self::assertNull($object->comment);
        self::assertSame('EUR', $object->currency);
    }

    public function testPassesPositionalArgumentsFollowedByNamed(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(ConstructorStub::class)
            ->instantiate([100, 'currency' => 'EUR']);

        self::assertSame(100, $object->amount);
        self::assertSame('EUR', $object->currency);
    }

    public function testIgnoresExtraPositionalArguments(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(ConstructorStub::class)
            ->instantiate([100, null, 'EUR', null, 'extra']);

        self::assertSame(100, $object->amount);
    }

    public function testPassesVariadicPositionalArguments(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(VariadicStub::class)
            ->instantiate([2, 'first', 'second']);

        self::assertSame(2, $object->count);
        self::assertSame(['first', 'second'], $object->tags);
    }

    public function testPassesVariadicNamedArguments(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(VariadicStub::class)
            ->instantiate(['count' => 2, 'first' => 'a', 'second' => 'b']);

        self::assertSame(['first' => 'a', 'second' => 'b'], $object->tags);
    }

    public function testCreatesObjectOfClassWithoutConstructor(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(EmptyStub::class)
            ->instantiate();

        self::assertInstanceOf(EmptyStub::class, $object);
    }

    public function testCreatesObjectWithInheritedConstructor(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromConstructor(ChildStub::class)
            ->instantiate(['value' => 42]);

        self::assertSame(42, $object->value);
    }

    public function testWrapsExceptionOfConstructor(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromConstructor(ThrowingStub::class);

        $error = self::captureThrowable(static fn() => $instantiator->instantiate());

        self::assertInstanceOf(ConstructionFailedException::class, $error);
        self::assertInstanceOf(\DomainException::class, $error->getPrevious());
        self::assertSame(
            \sprintf('Constructor of class "%s" has thrown an exception: Construction has failed', ThrowingStub::class),
            $error->getMessage(),
        );
    }

    public function testWrapsArgumentErrorOfConstructor(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromConstructor(ConstructorStub::class);

        $error = self::captureThrowable(static fn() => $instantiator->instantiate(['amount' => '100']));

        self::assertInstanceOf(ConstructionFailedException::class, $error);
        self::assertSame(ConstructorStub::class, $error->class);
        self::assertInstanceOf(\TypeError::class, $error->getPrevious());
    }
}
