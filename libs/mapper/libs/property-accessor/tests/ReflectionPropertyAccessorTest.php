<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\PropertyAccessor\Exception\MissingPropertyReadException;
use TypeLang\PropertyAccessor\Exception\MissingPropertyWriteException;
use TypeLang\PropertyAccessor\Exception\PropertyAlreadyInitializedException;
use TypeLang\PropertyAccessor\Exception\ReadOnlyPropertyException;
use TypeLang\PropertyAccessor\Exception\StaticPropertyReadException;
use TypeLang\PropertyAccessor\Exception\StaticPropertyWriteException;
use TypeLang\PropertyAccessor\Exception\UninitializedPropertyReadException;
use TypeLang\PropertyAccessor\ReflectionPropertyAccessor;
use TypeLang\PropertyAccessor\Tests\Stub\BaseStub;
use TypeLang\PropertyAccessor\Tests\Stub\HookStub;
use TypeLang\PropertyAccessor\Tests\Stub\InheritedStub;
use TypeLang\PropertyAccessor\Tests\Stub\ReadonlyStub;
use TypeLang\PropertyAccessor\Tests\Stub\StaticStub;
use TypeLang\PropertyAccessor\Tests\Stub\TraitConsumerStub;
use TypeLang\PropertyAccessor\Tests\Stub\UninitializedStub;
use TypeLang\PropertyAccessor\Tests\Stub\VisibilityStub;

final class ReflectionPropertyAccessorTest extends PropertyAccessorTestCase
{
    private ReflectionPropertyAccessor $accessor {
        get => $this->accessor ??= new ReflectionPropertyAccessor();
    }

    // ---------------------------------------------------------------------
    //  Access modifiers (public / protected / private, instance properties)
    // ---------------------------------------------------------------------

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function visibilityProvider(): iterable
    {
        yield 'public' => ['publicProperty', 'public-value'];
        yield 'protected' => ['protectedProperty', 'protected-value'];
        yield 'private' => ['privateProperty', 'private-value'];
    }

    #[DataProvider('visibilityProvider')]
    public function testReadsPropertyOfAnyVisibility(string $property, string $expected): void
    {
        self::assertSame($expected, $this->accessor->getValue(new VisibilityStub(), $property));
    }

    #[DataProvider('visibilityProvider')]
    public function testPropertyOfAnyVisibilityIsReadable(string $property, string $expected): void
    {
        self::assertTrue($this->accessor->isReadable(new VisibilityStub(), $property));
    }

    #[DataProvider('visibilityProvider')]
    public function testPropertyOfAnyVisibilityIsWritable(string $property, string $expected): void
    {
        self::assertTrue($this->accessor->isWritable(new VisibilityStub(), $property));
    }

    public function testWritesPublicProperty(): void
    {
        $object = new VisibilityStub();

        $this->accessor->setValue($object, 'publicProperty', 'changed');

        self::assertSame('changed', $object->publicProperty);
    }

    public function testWritesProtectedProperty(): void
    {
        $object = new VisibilityStub();

        $this->accessor->setValue($object, 'protectedProperty', 'changed');

        self::assertSame('changed', $object->getProtectedProperty());
    }

    public function testWritesPrivateProperty(): void
    {
        $object = new VisibilityStub();

        $this->accessor->setValue($object, 'privateProperty', 'changed');

        self::assertSame('changed', $object->getPrivateProperty());
    }

    // ---------------------------------------------------------------------
    //  Static properties (expected to be inaccessible via an instance accessor)
    // ---------------------------------------------------------------------

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function staticPropertyProvider(): iterable
    {
        yield 'public static' => ['publicStatic'];
        yield 'protected static' => ['protectedStatic'];
        yield 'private static' => ['privateStatic'];
    }

    #[DataProvider('staticPropertyProvider')]
    public function testStaticPropertyIsNotReadable(string $property): void
    {
        self::assertFalse($this->accessor->isReadable(new StaticStub(), $property));
    }

    #[DataProvider('staticPropertyProvider')]
    public function testStaticPropertyIsNotWritable(string $property): void
    {
        self::assertFalse($this->accessor->isWritable(new StaticStub(), $property));
    }

    #[DataProvider('staticPropertyProvider')]
    public function testGetValueOnStaticPropertyThrows(string $property): void
    {
        $this->expectException(StaticPropertyReadException::class);

        $this->accessor->getValue(new StaticStub(), $property);
    }

    #[DataProvider('staticPropertyProvider')]
    public function testSetValueOnStaticPropertyThrows(string $property): void
    {
        $this->expectException(StaticPropertyWriteException::class);

        $this->accessor->setValue(new StaticStub(), $property, 'x');
    }

    public function testInstancePropertyNextToStaticsRemainsAccessible(): void
    {
        $object = new StaticStub();

        self::assertTrue($this->accessor->isReadable($object, 'instanceProperty'));
        self::assertSame('instance-value', $this->accessor->getValue($object, 'instanceProperty'));
    }

    // ---------------------------------------------------------------------
    //  Readonly properties
    // ---------------------------------------------------------------------

    public function testReadonlyPropertyIsReadable(): void
    {
        self::assertTrue($this->accessor->isReadable(new ReadonlyStub(), 'readonlyProperty'));
    }

    public function testReadsReadonlyValue(): void
    {
        $actual = $this->accessor->getValue(new ReadonlyStub(), 'readonlyProperty');

        self::assertSame('readonly-value', $actual);
    }

    public function testInitializedReadonlyPropertyIsNotWritable(): void
    {
        self::assertFalse($this->accessor->isWritable(new ReadonlyStub(), 'readonlyProperty'));
    }

    public function testWritingInitializedReadonlyPropertyThrowsNotWritable(): void
    {
        $this->expectException(PropertyAlreadyInitializedException::class);

        $this->accessor->setValue(new ReadonlyStub(), 'readonlyProperty', 'other');
    }

    public function testUninitializedReadonlyPropertyIsWritable(): void
    {
        $object = new \ReflectionClass(ReadonlyStub::class)
            ->newInstanceWithoutConstructor();

        self::assertTrue($this->accessor->isWritable($object, 'readonlyProperty'));
    }

    public function testUninitializedReadonlyPropertyCanBeWrittenOnce(): void
    {
        $object = new \ReflectionClass(ReadonlyStub::class)
            ->newInstanceWithoutConstructor();

        $this->accessor->setValue($object, 'readonlyProperty', 'assigned');

        self::assertSame('assigned', $this->accessor->getValue($object, 'readonlyProperty'));

        $error = self::captureThrowable(fn(): mixed
            => $this->accessor->setValue($object, 'readonlyProperty', 'again'));

        self::assertInstanceOf(PropertyAlreadyInitializedException::class, $error);
    }

    // ---------------------------------------------------------------------
    //  Property hooks (PHP 8.4)
    // ---------------------------------------------------------------------

    public function testVirtualGetOnlyPropertyIsReadable(): void
    {
        self::assertTrue($this->accessor->isReadable(new HookStub(), 'virtualReadOnly'));
    }

    public function testReadsVirtualGetOnlyProperty(): void
    {
        self::assertSame('computed', $this->accessor->getValue(new HookStub(), 'virtualReadOnly'));
    }

    public function testVirtualGetOnlyPropertyIsNotWritable(): void
    {
        self::assertFalse($this->accessor->isWritable(new HookStub(), 'virtualReadOnly'));
    }

    public function testWritingVirtualGetOnlyPropertyThrowsNotWritable(): void
    {
        $this->expectException(ReadOnlyPropertyException::class);

        $this->accessor->setValue(new HookStub(), 'virtualReadOnly', 'x');
    }

    public function testHookedPropertyIsReadableAndWritable(): void
    {
        $object = new HookStub();

        self::assertTrue($this->accessor->isReadable($object, 'hooked'));
        self::assertTrue($this->accessor->isWritable($object, 'hooked'));
    }

    public function testGetValueReadsRawValueBypassingGetHook(): void
    {
        // The get hook would return 'via-get-hook'; raw access reads the backing store.
        self::assertSame('raw-init', $this->accessor->getValue(new HookStub(), 'hooked'));
    }

    public function testSetValueWritesRawValueBypassingSetHook(): void
    {
        $object = new HookStub();

        $this->accessor->setValue($object, 'hooked', 'written');

        // The set hook would store 'via-set-hook:written'; raw access stores the value verbatim.
        self::assertSame('written', new \ReflectionProperty($object, 'hooked')->getRawValue($object));
        // Reading back through the accessor likewise bypasses the get hook.
        self::assertSame('written', $this->accessor->getValue($object, 'hooked'));
    }

    // ---------------------------------------------------------------------
    //  Inheritance / scope (parent, child, parent-private)
    // ---------------------------------------------------------------------

    public function testReadsInheritedPublicProperty(): void
    {
        self::assertSame('base-public', $this->accessor->getValue(new InheritedStub(), 'basePublic'));
    }

    public function testReadsInheritedProtectedProperty(): void
    {
        self::assertSame('base-protected', $this->accessor->getValue(new InheritedStub(), 'baseProtected'));
    }

    /**
     * A private property belongs to the scope of the class that declares it, so
     * it is not visible by name from a derived class. The accessor must treat it
     * as inaccessible through a child instance (as PHP itself does), rather than
     * walking the hierarchy to reach it.
     */
    public function testParentPrivatePropertyIsNotReadableThroughChildInstance(): void
    {
        self::assertFalse($this->accessor->isReadable(new InheritedStub(), 'basePrivate'));
    }

    public function testGetValueOnParentPrivatePropertyThroughChildInstanceThrows(): void
    {
        $this->expectException(MissingPropertyReadException::class);

        $this->accessor->getValue(new InheritedStub(), 'basePrivate');
    }

    public function testReadsChildOwnProperty(): void
    {
        self::assertSame('child-public', $this->accessor->getValue(new InheritedStub(), 'childPublic'));
        self::assertSame('child-private', $this->accessor->getValue(new InheritedStub(), 'childPrivate'));
    }

    public function testWritesInheritedProperty(): void
    {
        $object = new InheritedStub();

        $this->accessor->setValue($object, 'basePublic', 'updated');

        self::assertSame('updated', $object->basePublic);
    }

    public function testParentPrivatePropertyIsNotWritableThroughChildInstance(): void
    {
        self::assertFalse($this->accessor->isWritable(new InheritedStub(), 'basePrivate'));
    }

    public function testSetValueOnParentPrivatePropertyThroughChildInstanceThrows(): void
    {
        $this->expectException(MissingPropertyWriteException::class);

        $this->accessor->setValue(new InheritedStub(), 'basePrivate', 'updated');
    }

    public function testReadsPropertyOnParentInstanceDirectly(): void
    {
        self::assertSame('base-private', $this->accessor
            ->getValue(new BaseStub(), 'basePrivate'));
    }

    // ---------------------------------------------------------------------
    //  Trait properties
    // ---------------------------------------------------------------------

    public function testReadsPublicTraitProperty(): void
    {
        self::assertSame('trait-public', $this->accessor
            ->getValue(new TraitConsumerStub(), 'traitPublic'));
    }

    public function testReadsPrivateTraitProperty(): void
    {
        self::assertSame('trait-private', $this->accessor
            ->getValue(new TraitConsumerStub(), 'traitPrivate'));
    }

    public function testWritesTraitProperty(): void
    {
        $object = new TraitConsumerStub();

        $this->accessor->setValue($object, 'traitPrivate', 'updated');

        self::assertSame('updated', $object->getTraitPrivate());
    }

    // ---------------------------------------------------------------------
    //  Uninitialized typed property
    // ---------------------------------------------------------------------

    public function testUninitializedPropertyIsNotReadable(): void
    {
        self::assertFalse($this->accessor->isReadable(new UninitializedStub(), 'uninitialized'));
    }

    public function testGetValueOnUninitializedPropertyThrowsNotReadable(): void
    {
        $this->expectException(UninitializedPropertyReadException::class);

        $this->accessor->getValue(new UninitializedStub(), 'uninitialized');
    }

    public function testUninitializedPropertyIsWritable(): void
    {
        self::assertTrue($this->accessor->isWritable(new UninitializedStub(), 'uninitialized'));
    }

    public function testWritingUninitializedPropertyMakesItReadable(): void
    {
        $object = new UninitializedStub();

        $this->accessor->setValue($object, 'uninitialized', 'now-set');

        self::assertTrue($this->accessor->isReadable($object, 'uninitialized'));
        self::assertSame('now-set', $this->accessor->getValue($object, 'uninitialized'));
    }

    // ---------------------------------------------------------------------
    //  Missing property
    // ---------------------------------------------------------------------

    public function testMissingPropertyIsNotReadable(): void
    {
        self::assertFalse($this->accessor->isReadable(new VisibilityStub(), 'doesNotExist'));
    }

    public function testMissingPropertyIsNotWritable(): void
    {
        self::assertFalse($this->accessor->isWritable(new VisibilityStub(), 'doesNotExist'));
    }

    public function testGetValueOnMissingPropertyThrowsNotReadable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(fn(): mixed
            => $this->accessor->getValue($object, 'doesNotExist'));

        self::assertInstanceOf(MissingPropertyReadException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('doesNotExist', $error->property);
    }

    public function testSetValueOnMissingPropertyThrowsNotWritable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable( fn(): mixed
            => $this->accessor->setValue($object, 'doesNotExist', 'x'));

        self::assertInstanceOf(MissingPropertyWriteException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('doesNotExist', $error->property);
    }
}
