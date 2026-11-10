<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\Mapper\PropertyAccessor\NullPropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\VisibilityStub;

final class NullPropertyAccessorTest extends PropertyAccessorTestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function propertyNameProvider(): iterable
    {
        yield 'public property' => ['publicProperty'];
        yield 'missing property' => ['doesNotExist'];
    }

    #[DataProvider('propertyNameProvider')]
    public function testIsReadableAlwaysReturnsFalse(string $property): void
    {
        self::assertFalse((new NullPropertyAccessor(new VisibilityStub()))->isReadable($property));
    }

    #[DataProvider('propertyNameProvider')]
    public function testIsWritableAlwaysReturnsFalse(string $property): void
    {
        self::assertFalse((new NullPropertyAccessor(new VisibilityStub()))->isWritable($property));
    }

    public function testReadThrowsNotReadable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(static fn(): mixed => (new NullPropertyAccessor($object))
            ->read('publicProperty'));

        self::assertInstanceOf(PropertyNotReadableException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('publicProperty', $error->property);
        self::assertStringContainsString('disabled', $error->getMessage());
    }

    public function testWriteThrowsNotWritable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(static fn(): mixed => (new NullPropertyAccessor($object))
            ->write('publicProperty', 'x'));

        self::assertInstanceOf(PropertyNotWritableException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('publicProperty', $error->property);
        self::assertStringContainsString('disabled', $error->getMessage());
    }

    public function testReadDoesNotMutateReadableState(): void
    {
        $accessor = new NullPropertyAccessor(new VisibilityStub());

        self::captureThrowable(static fn(): mixed => $accessor->read('publicProperty'));

        self::assertFalse($accessor->isReadable('publicProperty'));
    }
}
