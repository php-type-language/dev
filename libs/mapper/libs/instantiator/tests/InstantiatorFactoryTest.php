<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Instantiator\ConstructorInstantiator;
use TypeLang\Mapper\Instantiator\Exception\ClassNotFoundException;
use TypeLang\Mapper\Instantiator\Exception\ClassNotInstantiableException;
use TypeLang\Mapper\Instantiator\Exception\MethodNotCallableException;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Instantiator\StaticMethodInstantiator;
use TypeLang\Mapper\Instantiator\Tests\Stub\AbstractStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\ConstructorStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EmptyStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EnumStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\InterfaceStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\PrivateConstructorStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\StaticMethodStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\TraitStub;
use TypeLang\Mapper\Instantiator\WithoutConstructorInstantiator;

final class InstantiatorFactoryTest extends TestCase
{
    /**
     * @var non-empty-string
     */
    private const MISSING_CLASS = 'TypeLang\Mapper\Instantiator\Tests\Stub\MissingStub';

    /**
     * @return iterable<non-empty-string, array{class-string, non-empty-string}>
     */
    public static function nonInstantiableClassDataProvider(): iterable
    {
        yield 'interface' => [InterfaceStub::class, 'Cannot instantiate interface "%s"'];
        yield 'trait' => [TraitStub::class, 'Cannot instantiate trait "%s"'];
        yield 'enum' => [EnumStub::class, 'Cannot instantiate enum "%s"'];
        yield 'abstract class' => [AbstractStub::class, 'Cannot instantiate abstract class "%s"'];
    }

    /**
     * @return iterable<non-empty-string, array{
     *     class-string,
     *     non-empty-string,
     *     non-empty-string,
     * }>
     */
    public static function notCallableMethodDataProvider(): iterable
    {
        yield 'method does not exist' => [
            StaticMethodStub::class,
            'createMissing',
            'Method "%s::createMissing()" does not exist',
        ];
        yield 'method is protected' => [
            StaticMethodStub::class,
            'createProtected',
            'Method "%s::createProtected()" is not public',
        ];
        yield 'method is not static' => [
            StaticMethodStub::class,
            'createInstance',
            'Method "%s::createInstance()" is not static',
        ];
        yield 'method is abstract' => [
            AbstractStub::class,
            'createAbstract',
            'Method "%s::createAbstract()" is abstract',
        ];
        yield 'method of interface is abstract' => [
            InterfaceStub::class,
            'create',
            'Method "%s::create()" is abstract',
        ];
        yield 'constructor is not static' => [
            StaticMethodStub::class,
            '__construct',
            'Method "%s::__construct()" is not static',
        ];
    }

    public function testCreatesInstantiatorWithoutConstructor(): void
    {
        $instantiator = (new InstantiatorFactory())->createWithoutConstructor(ConstructorStub::class);

        self::assertInstanceOf(WithoutConstructorInstantiator::class, $instantiator);
    }

    public function testCreatesInstantiatorFromConstructor(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromConstructor(ConstructorStub::class);

        self::assertInstanceOf(ConstructorInstantiator::class, $instantiator);
    }

    public function testCreatesInstantiatorFromAbsentConstructor(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromConstructor(EmptyStub::class);

        self::assertInstanceOf(ConstructorInstantiator::class, $instantiator);
        self::assertInstanceOf(EmptyStub::class, $instantiator->instantiate());
    }

    public function testCreatesInstantiatorFromStaticMethod(): void
    {
        $instantiator = (new InstantiatorFactory())->createFromStaticMethod(StaticMethodStub::class, 'create');

        self::assertInstanceOf(StaticMethodInstantiator::class, $instantiator);
    }

    public function testRejectsMissingClassWithoutConstructor(): void
    {
        $error = self::captureThrowable(static fn() => (new InstantiatorFactory())
            // @phpstan-ignore-next-line the class is missing on purpose
            ->createWithoutConstructor(self::MISSING_CLASS));

        self::assertInstanceOf(ClassNotFoundException::class, $error);
        self::assertSame(self::MISSING_CLASS, $error->class);
    }

    public function testRejectsMissingClassFromConstructor(): void
    {
        $this->expectException(ClassNotFoundException::class);

        // @phpstan-ignore-next-line the class is missing on purpose
        (new InstantiatorFactory())->createFromConstructor(self::MISSING_CLASS);
    }

    public function testRejectsMissingClassFromStaticMethod(): void
    {
        $this->expectException(ClassNotFoundException::class);

        // @phpstan-ignore-next-line the class is missing on purpose
        (new InstantiatorFactory())->createFromStaticMethod(self::MISSING_CLASS, 'create');
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('nonInstantiableClassDataProvider')]
    public function testRejectsNonInstantiableClassWithoutConstructor(string $class, string $message): void
    {
        $error = self::captureThrowable(static fn() => (new InstantiatorFactory())
            ->createWithoutConstructor($class));

        self::assertInstanceOf(ClassNotInstantiableException::class, $error);
        self::assertSame(\sprintf($message, $class), $error->getMessage());
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('nonInstantiableClassDataProvider')]
    public function testRejectsNonInstantiableClassFromConstructor(string $class, string $message): void
    {
        $error = self::captureThrowable(static fn() => (new InstantiatorFactory())
            ->createFromConstructor($class));

        self::assertInstanceOf(ClassNotInstantiableException::class, $error);
        self::assertSame(\sprintf($message, $class), $error->getMessage());
    }

    /**
     * @return iterable<non-empty-string, array{class-string}>
     */
    public static function internalFinalClassDataProvider(): iterable
    {
        yield 'closure' => [\Closure::class];
        yield 'generator' => [\Generator::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('internalFinalClassDataProvider')]
    public function testRejectsInternalFinalClassWithoutConstructor(string $class): void
    {
        $error = self::captureThrowable(static fn() => (new InstantiatorFactory())
            ->createWithoutConstructor($class));

        self::assertEquals(ClassNotInstantiableException::becauseClassIsInternalFinal($class), $error);
    }

    public function testCreatesInternalClassWithoutConstructor(): void
    {
        $object = (new InstantiatorFactory())
            ->createWithoutConstructor(\ArrayObject::class)
            ->instantiate();

        self::assertInstanceOf(\ArrayObject::class, $object);
    }

    public function testRejectsPrivateConstructor(): void
    {
        $error = self::captureThrowable(static fn() => (new InstantiatorFactory())
            ->createFromConstructor(PrivateConstructorStub::class));

        self::assertInstanceOf(MethodNotCallableException::class, $error);
        self::assertSame(
            \sprintf('Constructor of class "%s" is not public', PrivateConstructorStub::class),
            $error->getMessage(),
        );
    }

    public function testRejectsClosureConstructor(): void
    {
        $this->expectException(MethodNotCallableException::class);

        (new InstantiatorFactory())->createFromConstructor(\Closure::class);
    }

    /**
     * @param class-string $class
     * @param non-empty-string $method
     */
    #[DataProvider('notCallableMethodDataProvider')]
    public function testRejectsNotCallableStaticMethod(string $class, string $method, string $message): void
    {
        $error = self::captureThrowable(static fn() => (new InstantiatorFactory())
            ->createFromStaticMethod($class, $method));

        self::assertInstanceOf(MethodNotCallableException::class, $error);
        self::assertSame($class, $error->class);
        self::assertSame(\sprintf($message, $class), $error->getMessage());
    }
}
