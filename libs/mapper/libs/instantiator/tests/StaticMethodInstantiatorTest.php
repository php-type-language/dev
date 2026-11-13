<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests;

use TypeLang\Mapper\Instantiator\Exception\ConstructionFailedException;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Instantiator\Tests\Stub\AbstractStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\ChildStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EnumStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\PrivateConstructorStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\StaticMethodStub;

final class StaticMethodInstantiatorTest extends TestCase
{
    public function testCallsFactoryOfClassWithPrivateConstructor(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromStaticMethod(PrivateConstructorStub::class, 'fromString')
            ->instantiate(['value' => 'example']);

        self::assertSame('example', $object->value);
    }

    public function testPassesPositionalArguments(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromStaticMethod(StaticMethodStub::class, 'create')
            ->instantiate([42]);

        self::assertSame(42, $object->value);
    }

    public function testCallsFactoryOfAbstractClass(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromStaticMethod(AbstractStub::class, 'create')
            ->instantiate();

        self::assertInstanceOf(AbstractStub::class, $object);
    }

    public function testCallsFactoryOfEnum(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromStaticMethod(EnumStub::class, 'fromLabel')
            ->instantiate(['label' => 'on']);

        self::assertSame(EnumStub::Active, $object);
    }

    public function testCallsInheritedFactoryBoundToSubclass(): void
    {
        $object = (new InstantiatorFactory())
            ->createFromStaticMethod(ChildStub::class, 'create')
            ->instantiate([42]);

        self::assertInstanceOf(ChildStub::class, $object);
    }

    public function testWrapsExceptionOfMethod(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromStaticMethod(StaticMethodStub::class, 'createFailing');

        $error = self::captureThrowable(static fn() => $instantiator->instantiate());

        self::assertInstanceOf(ConstructionFailedException::class, $error);
        self::assertInstanceOf(\DomainException::class, $error->getPrevious());
        self::assertSame(
            \sprintf(
                'Method "%s::createFailing()" has thrown an exception: Factory has failed',
                StaticMethodStub::class,
            ),
            $error->getMessage(),
        );
    }

    public function testWrapsArgumentErrorOfMethod(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromStaticMethod(StaticMethodStub::class, 'create');

        $error = self::captureThrowable(static fn() => $instantiator->instantiate(['unknown' => 42]));

        self::assertInstanceOf(ConstructionFailedException::class, $error);
        self::assertSame(StaticMethodStub::class, $error->class);
        self::assertInstanceOf(\Error::class, $error->getPrevious());
    }

    public function testRejectsObjectOfAnotherClass(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromStaticMethod(StaticMethodStub::class, 'createForeign');

        $error = self::captureThrowable(static fn() => $instantiator->instantiate());

        self::assertInstanceOf(ConstructionFailedException::class, $error);
        self::assertSame(
            \sprintf(
                'Method "%1$s::createForeign()" must return an instance of "%1$s", stdClass returned',
                StaticMethodStub::class,
            ),
            $error->getMessage(),
        );
    }

    public function testRejectsScalar(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromStaticMethod(StaticMethodStub::class, 'createScalar');

        $error = self::captureThrowable(static fn() => $instantiator->instantiate());

        self::assertInstanceOf(ConstructionFailedException::class, $error);
        self::assertStringEndsWith(', int returned', $error->getMessage());
    }
}
