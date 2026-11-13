<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\RequiresPhp;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\Mapper\PropertyAccessor\ModernReflectionPropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\HookStub;

#[RequiresPhp('>= 8.4.0')]
final class ModernReflectionPropertyAccessorTest extends ReflectionPropertyAccessorTestCase
{
    protected static function createPrototype(\ReflectionClass $class): PropertyAccessor
    {
        return new ModernReflectionPropertyAccessor($class);
    }

    public function testVirtualGetOnlyPropertyIsReadable(): void
    {
        self::assertTrue($this->accessor(new HookStub())->isReadable('virtualReadOnly'));
    }

    public function testReadsVirtualGetOnlyProperty(): void
    {
        self::assertSame('computed', $this->accessor(new HookStub())->read('virtualReadOnly'));
    }

    public function testVirtualGetOnlyPropertyIsNotWritable(): void
    {
        self::assertFalse($this->accessor(new HookStub())->isWritable('virtualReadOnly'));
    }

    public function testWritingVirtualGetOnlyPropertyThrowsNotWritable(): void
    {
        $this->expectException(PropertyNotWritableException::class);

        $this->accessor(new HookStub())->write('virtualReadOnly', 'x');
    }

    public function testHookedPropertyIsReadableAndWritable(): void
    {
        $object = new HookStub();

        self::assertTrue($this->accessor($object)->isReadable('hooked'));
        self::assertTrue($this->accessor($object)->isWritable('hooked'));
    }

    public function testGetValueReadsRawValueBypassingGetHook(): void
    {
        // The get hook returns 'via-get-hook', the backing store holds 'raw-init'
        self::assertSame('raw-init', $this->accessor(new HookStub())->read('hooked'));
    }

    public function testSetValueWritesRawValueBypassingSetHook(): void
    {
        $object = new HookStub();

        $this->accessor($object)->write('hooked', 'written');

        // The set hook stores 'via-set-hook:written'
        self::assertSame('written', (new \ReflectionProperty($object, 'hooked'))->getRawValue($object));
        self::assertSame('written', $this->accessor($object)->read('hooked'));
    }
}
