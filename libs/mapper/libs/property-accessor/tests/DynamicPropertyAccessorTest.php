<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests;

use TypeLang\Mapper\PropertyAccessor\DynamicPropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;

final class DynamicPropertyAccessorTest extends PropertyAccessorTestCase
{
    public function testReadsExistingProperty(): void
    {
        $object = new \stdClass();
        $object->name = 'value';

        $accessor = (new DynamicPropertyAccessor())->withContext($object);

        self::assertTrue($accessor->isReadable('name'));
        self::assertSame('value', $accessor->read('name'));
    }

    public function testReadsNullProperty(): void
    {
        $object = new \stdClass();
        $object->name = null;

        $accessor = (new DynamicPropertyAccessor())->withContext($object);

        self::assertTrue($accessor->isReadable('name'));
        self::assertNull($accessor->read('name'));
    }

    public function testMissingPropertyIsNotReadable(): void
    {
        $accessor = (new DynamicPropertyAccessor())->withContext(new \stdClass());

        self::assertFalse($accessor->isReadable('doesNotExist'));
    }

    public function testReadOfMissingPropertyThrows(): void
    {
        $object = new \stdClass();

        $error = self::captureThrowable(static fn(): mixed => (new DynamicPropertyAccessor())
            ->withContext($object)
            ->read('doesNotExist'));

        self::assertInstanceOf(PropertyNotFoundException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('doesNotExist', $error->property);
    }

    public function testWriteCreatesMissingProperty(): void
    {
        $object = new \stdClass();

        $accessor = (new DynamicPropertyAccessor())->withContext($object);

        self::assertTrue($accessor->isWritable('name'));

        $accessor->write('name', 'value');

        self::assertSame('value', $object->name);
    }
}
