<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\PropertyAccessor\Exception\InvalidPropertyValueException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAlreadyInitializedException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;
use TypeLang\Mapper\PropertyAccessor\Exception\UninitializedPropertyException;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorInterface;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\BaseStub;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\InheritedStub;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\ReadonlyStub;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\StaticStub;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\TraitConsumerStub;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\UninitializedStub;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\VisibilityStub;

/**
 * Behaviour shared by the reflection-based accessors on properties without
 * hooks.
 */
abstract class ReflectionPropertyAccessorTestCase extends PropertyAccessorTestCase
{
    /**
     * @param \ReflectionClass<object> $class
     * @return PropertyAccessor<object>
     */
    abstract protected static function createPrototype(\ReflectionClass $class): PropertyAccessor;

    /**
     * @template T of object
     *
     * @param T $object
     * @return PropertyAccessorInterface<T>
     */
    protected function accessor(object $object): PropertyAccessorInterface
    {
        return static::createPrototype(new \ReflectionClass($object))
            ->withContext($object);
    }

    public function testPrototypeCopiesAreBoundToTheirOwnObjects(): void
    {
        $prototype = static::createPrototype(new \ReflectionClass(VisibilityStub::class));

        $first = new VisibilityStub();
        $second = new VisibilityStub();

        $prototype->withContext($first)->write('privateProperty', 'first');
        $prototype->withContext($second)->write('privateProperty', 'second');

        self::assertSame('first', $first->getPrivateProperty());
        self::assertSame('second', $second->getPrivateProperty());
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
        self::assertSame($expected, $this->accessor(new VisibilityStub())->read($property));
    }

    #[DataProvider('visibilityProvider')]
    public function testPropertyOfAnyVisibilityIsReadable(string $property, string $expected): void
    {
        self::assertTrue($this->accessor(new VisibilityStub())->isReadable($property));
    }

    #[DataProvider('visibilityProvider')]
    public function testPropertyOfAnyVisibilityIsWritable(string $property, string $expected): void
    {
        self::assertTrue($this->accessor(new VisibilityStub())->isWritable($property));
    }

    public function testWritesPublicProperty(): void
    {
        $object = new VisibilityStub();

        $this->accessor($object)->write('publicProperty', 'changed');

        self::assertSame('changed', $object->publicProperty);
    }

    public function testWritingValueOfInvalidTypeThrowsInvalidValue(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(fn(): mixed => $this->accessor($object)
            ->write('privateProperty', []));

        self::assertInstanceOf(InvalidPropertyValueException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('privateProperty', $error->property);
        self::assertInstanceOf(\TypeError::class, $error->getPrevious());
        self::assertSame(\sprintf(
            'Property %s::$privateProperty of type string cannot be assigned a value of type array',
            VisibilityStub::class,
        ), $error->getMessage());
    }

    public function testWritesProtectedProperty(): void
    {
        $object = new VisibilityStub();

        $this->accessor($object)->write('protectedProperty', 'changed');

        self::assertSame('changed', $object->getProtectedProperty());
    }

    public function testWritesPrivateProperty(): void
    {
        $object = new VisibilityStub();

        $this->accessor($object)->write('privateProperty', 'changed');

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
        self::assertFalse($this->accessor(new StaticStub())->isReadable($property));
    }

    #[DataProvider('staticPropertyProvider')]
    public function testStaticPropertyIsNotWritable(string $property): void
    {
        self::assertFalse($this->accessor(new StaticStub())->isWritable($property));
    }

    #[DataProvider('staticPropertyProvider')]
    public function testGetValueOnStaticPropertyThrows(string $property): void
    {
        $this->expectException(PropertyNotFoundException::class);

        $this->accessor(new StaticStub())->read($property);
    }

    #[DataProvider('staticPropertyProvider')]
    public function testSetValueOnStaticPropertyThrows(string $property): void
    {
        $this->expectException(PropertyNotFoundException::class);

        $this->accessor(new StaticStub())->write($property, 'x');
    }

    public function testInstancePropertyNextToStaticsRemainsAccessible(): void
    {
        $object = new StaticStub();

        self::assertTrue($this->accessor($object)->isReadable('instanceProperty'));
        self::assertSame('instance-value', $this->accessor($object)->read('instanceProperty'));
    }

    // ---------------------------------------------------------------------
    //  Readonly properties
    // ---------------------------------------------------------------------

    public function testReadonlyPropertyIsReadable(): void
    {
        self::assertTrue($this->accessor(new ReadonlyStub())->isReadable('readonlyProperty'));
    }

    public function testReadsReadonlyValue(): void
    {
        $actual = $this->accessor(new ReadonlyStub())->read('readonlyProperty');

        self::assertSame('readonly-value', $actual);
    }

    public function testInitializedReadonlyPropertyIsNotWritable(): void
    {
        self::assertFalse($this->accessor(new ReadonlyStub())->isWritable('readonlyProperty'));
    }

    public function testWritingInitializedReadonlyPropertyThrowsNotWritable(): void
    {
        $this->expectException(PropertyAlreadyInitializedException::class);

        $this->accessor(new ReadonlyStub())->write('readonlyProperty', 'other');
    }

    public function testUninitializedReadonlyPropertyIsWritable(): void
    {
        $object = (new \ReflectionClass(ReadonlyStub::class))
            ->newInstanceWithoutConstructor();

        self::assertTrue($this->accessor($object)->isWritable('readonlyProperty'));
    }

    public function testUninitializedReadonlyPropertyCanBeWrittenOnce(): void
    {
        $object = (new \ReflectionClass(ReadonlyStub::class))
            ->newInstanceWithoutConstructor();

        $this->accessor($object)->write('readonlyProperty', 'assigned');

        self::assertSame('assigned', $this->accessor($object)->read('readonlyProperty'));

        $error = self::captureThrowable(fn(): mixed
            => $this->accessor($object)->write('readonlyProperty', 'again'));

        self::assertInstanceOf(PropertyAlreadyInitializedException::class, $error);
    }

    // ---------------------------------------------------------------------
    //  Inheritance / scope (parent, child, parent-private)
    // ---------------------------------------------------------------------

    public function testReadsInheritedPublicProperty(): void
    {
        self::assertSame('base-public', $this->accessor(new InheritedStub())->read('basePublic'));
    }

    public function testReadsInheritedProtectedProperty(): void
    {
        self::assertSame('base-protected', $this->accessor(new InheritedStub())->read('baseProtected'));
    }

    /**
     * A private property belongs to the scope of the class that declares it, so
     * it is not visible by name from a derived class. The accessor must treat it
     * as inaccessible through a child instance (as PHP itself does), rather than
     * walking the hierarchy to reach it.
     */
    public function testParentPrivatePropertyIsNotReadableThroughChildInstance(): void
    {
        self::assertFalse($this->accessor(new InheritedStub())->isReadable('basePrivate'));
    }

    public function testGetValueOnParentPrivatePropertyThroughChildInstanceThrows(): void
    {
        $this->expectException(PropertyNotFoundException::class);

        $this->accessor(new InheritedStub())->read('basePrivate');
    }

    public function testReadsChildOwnProperty(): void
    {
        self::assertSame('child-public', $this->accessor(new InheritedStub())->read('childPublic'));
        self::assertSame('child-private', $this->accessor(new InheritedStub())->read('childPrivate'));
    }

    public function testWritesInheritedProperty(): void
    {
        $object = new InheritedStub();

        $this->accessor($object)->write('basePublic', 'updated');

        self::assertSame('updated', $object->basePublic);
    }

    public function testParentPrivatePropertyIsNotWritableThroughChildInstance(): void
    {
        self::assertFalse($this->accessor(new InheritedStub())->isWritable('basePrivate'));
    }

    public function testSetValueOnParentPrivatePropertyThroughChildInstanceThrows(): void
    {
        $this->expectException(PropertyNotFoundException::class);

        $this->accessor(new InheritedStub())->write('basePrivate', 'updated');
    }

    public function testReadsPropertyOnParentInstanceDirectly(): void
    {
        self::assertSame('base-private', $this->accessor(new BaseStub())
            ->read('basePrivate'));
    }

    // ---------------------------------------------------------------------
    //  Trait properties
    // ---------------------------------------------------------------------

    public function testReadsPublicTraitProperty(): void
    {
        self::assertSame('trait-public', $this->accessor(new TraitConsumerStub())
            ->read('traitPublic'));
    }

    public function testReadsPrivateTraitProperty(): void
    {
        self::assertSame('trait-private', $this->accessor(new TraitConsumerStub())
            ->read('traitPrivate'));
    }

    public function testWritesTraitProperty(): void
    {
        $object = new TraitConsumerStub();

        $this->accessor($object)->write('traitPrivate', 'updated');

        self::assertSame('updated', $object->getTraitPrivate());
    }

    // ---------------------------------------------------------------------
    //  Uninitialized typed property
    // ---------------------------------------------------------------------

    public function testUninitializedPropertyIsNotReadable(): void
    {
        self::assertFalse($this->accessor(new UninitializedStub())->isReadable('uninitialized'));
    }

    public function testGetValueOnUninitializedPropertyThrowsNotReadable(): void
    {
        $this->expectException(UninitializedPropertyException::class);

        $this->accessor(new UninitializedStub())->read('uninitialized');
    }

    public function testUninitializedPropertyIsWritable(): void
    {
        self::assertTrue($this->accessor(new UninitializedStub())->isWritable('uninitialized'));
    }

    public function testWritingUninitializedPropertyMakesItReadable(): void
    {
        $object = new UninitializedStub();

        $this->accessor($object)->write('uninitialized', 'now-set');

        self::assertTrue($this->accessor($object)->isReadable('uninitialized'));
        self::assertSame('now-set', $this->accessor($object)->read('uninitialized'));
    }

    // ---------------------------------------------------------------------
    //  Missing property
    // ---------------------------------------------------------------------

    public function testMissingPropertyIsNotReadable(): void
    {
        self::assertFalse($this->accessor(new VisibilityStub())->isReadable('doesNotExist'));
    }

    public function testMissingPropertyIsNotWritable(): void
    {
        self::assertFalse($this->accessor(new VisibilityStub())->isWritable('doesNotExist'));
    }

    public function testGetValueOnMissingPropertyThrowsNotReadable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable(fn(): mixed
            => $this->accessor($object)->read('doesNotExist'));

        self::assertInstanceOf(PropertyNotFoundException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('doesNotExist', $error->property);
    }

    public function testSetValueOnMissingPropertyThrowsNotWritable(): void
    {
        $object = new VisibilityStub();

        $error = self::captureThrowable( fn(): mixed
            => $this->accessor($object)->write('doesNotExist', 'x'));

        self::assertInstanceOf(PropertyNotFoundException::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('doesNotExist', $error->property);
    }
}
