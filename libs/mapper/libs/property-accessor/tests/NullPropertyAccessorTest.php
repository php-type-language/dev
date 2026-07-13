<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\PropertyAccessor\NullPropertyAccessor;
use TypeLang\PropertyAccessor\Tests\Stub\VisibilityStub;

final class NullPropertyAccessorTest extends PropertyAccessorTestCase
{
    private NullPropertyAccessor $accessor {
        get => $this->accessor ??= new NullPropertyAccessor();
    }

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
        self::assertFalse($this->accessor->isReadable(new VisibilityStub(), $property));
    }

    #[DataProvider('propertyNameProvider')]
    public function testIsWritableAlwaysReturnsFalse(string $property): void
    {
        self::assertFalse($this->accessor->isWritable(new VisibilityStub(), $property));
    }

    public function testGetValueThrowsNotReadable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(static fn(): mixed => new NullPropertyAccessor()
            ->getValue($object, 'publicProperty'));

        self::assertInstanceOf(PropertyNotReadableException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('publicProperty', $error->property);
        self::assertStringContainsString('not readable', $error->getMessage());
    }

    public function testSetValueThrowsNotWritable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(static fn(): mixed => new NullPropertyAccessor()
            ->setValue($object, 'publicProperty', 'x'));

        self::assertInstanceOf(PropertyNotWritableException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('publicProperty', $error->property);
        self::assertStringContainsString('not writable', $error->getMessage());
    }

    public function testGetValueDoesNotMutateReadableState(): void
    {
        $object = new VisibilityStub();

        self::captureThrowable(fn(): mixed => $this->accessor->getValue($object, 'publicProperty'));

        self::assertFalse($this->accessor->isReadable($object, 'publicProperty'));
    }
}
