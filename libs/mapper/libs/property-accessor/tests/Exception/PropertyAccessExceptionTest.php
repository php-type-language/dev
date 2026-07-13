<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests\Exception;

use TypeLang\PropertyAccessor\Exception\PropertyAccessException;
use TypeLang\PropertyAccessor\Exception\PropertyAccessorExceptionInterface;
use TypeLang\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\PropertyAccessor\Tests\PropertyAccessorTestCase;
use TypeLang\PropertyAccessor\Tests\Stub\VisibilityStub;

final class PropertyAccessExceptionTest extends PropertyAccessorTestCase
{
    public function testNotReadableFactoryCapturesContext(): void
    {
        $object = new VisibilityStub();

        $error = PropertyNotReadableException::becausePropertyIsNotReadable($object, 'publicProperty');

        self::assertSame($object, $error->object);
        self::assertSame('publicProperty', $error->property);
        self::assertSame(
            \sprintf('The %s::$publicProperty property is not readable', VisibilityStub::class),
            $error->getMessage(),
        );
    }

    public function testNotWritableFactoryCapturesContext(): void
    {
        $object = new VisibilityStub();

        $error = PropertyNotWritableException::becausePropertyIsNotWritable($object, 'publicProperty');

        self::assertSame($object, $error->object);
        self::assertSame('publicProperty', $error->property);
        self::assertSame(
            \sprintf('The %s::$publicProperty property is not writable', VisibilityStub::class),
            $error->getMessage(),
        );
    }

    public function testNotReadablePreservesPreviousThrowable(): void
    {
        $previous = new \RuntimeException('root cause');

        $error = PropertyNotReadableException::becausePropertyIsNotReadable(
            new VisibilityStub(),
            'publicProperty',
            $previous,
        );

        self::assertSame($previous, $error->getPrevious());
    }

    public function testNotWritablePreservesPreviousThrowable(): void
    {
        $previous = new \RuntimeException('root cause');

        $error = PropertyNotWritableException::becausePropertyIsNotWritable(
            new VisibilityStub(),
            'publicProperty',
            $previous,
        );

        self::assertSame($previous, $error->getPrevious());
    }

    public function testExceptionsImplementPackageInterface(): void
    {
        $readable = PropertyNotReadableException::becausePropertyIsNotReadable(new VisibilityStub(), 'p');
        $writable = PropertyNotWritableException::becausePropertyIsNotWritable(new VisibilityStub(), 'p');

        self::assertInstanceOf(PropertyAccessorExceptionInterface::class, $readable);
        self::assertInstanceOf(PropertyAccessorExceptionInterface::class, $writable);
        self::assertInstanceOf(PropertyAccessException::class, $readable);
        self::assertInstanceOf(PropertyAccessException::class, $writable);
        self::assertInstanceOf(\LogicException::class, $readable);
    }
}
