<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Instantiator\Exception\ClassNotInstantiableException;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Instantiator\InstantiatorInterface;
use TypeLang\Mapper\Instantiator\Tests\Stub\AbstractStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\ConstructorStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EmptyStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EnumStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\InterfaceStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\TraitStub;

final class InstantiatorFactoryTest extends TestCase
{
    public function testDoesNotCallConstructor(): void
    {
        $object = self::createInstantiator(ConstructorStub::class)->instantiate();

        self::assertInstanceOf(ConstructorStub::class, $object);
        self::assertFalse($object->isConstructed);
    }

    public function testInitializesDefaultPropertyValues(): void
    {
        self::assertSame('default', self::createInstantiator(EmptyStub::class)->instantiate()->value);
    }

    public function testCreatesDistinctObjects(): void
    {
        $instantiator = self::createInstantiator(EmptyStub::class);

        $first = $instantiator->instantiate();
        $first->value = 'changed';

        $second = $instantiator->instantiate();

        self::assertNotSame($first, $second);
        self::assertSame('default', $second->value);
    }

    public function testCreatesInternalClassThatNeedsNoConstructor(): void
    {
        self::assertInstanceOf(\stdClass::class, self::createInstantiator(\stdClass::class)->instantiate());
    }

    /**
     * @return iterable<non-empty-string, array{class-string, non-empty-string}>
     */
    public static function notInstantiableDataProvider(): iterable
    {
        yield 'abstract class' => [AbstractStub::class, 'Cannot instantiate abstract class "%s"'];
        yield 'interface' => [InterfaceStub::class, 'Cannot instantiate interface "%s"'];
        yield 'trait' => [TraitStub::class, 'Cannot instantiate trait "%s"'];
        yield 'enum' => [EnumStub::class, 'Cannot instantiate enum "%s"'];
        yield 'internal final class' => [\Closure::class, 'Cannot instantiate class "%s" without the constructor'];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('notInstantiableDataProvider')]
    public function testClassIsRejectedOnCreationOfInstantiator(string $class, string $message): void
    {
        $error = self::captureThrowable(static fn(): object => self::createInstantiator($class));

        self::assertInstanceOf(ClassNotInstantiableException::class, $error);
        self::assertSame($class, $error->class);
        self::assertSame(\sprintf($message, $class), $error->getMessage());
    }

    /**
     * @template TArgObject of object
     * @param class-string<TArgObject> $class
     * @return InstantiatorInterface<TArgObject>
     */
    private static function createInstantiator(string $class): InstantiatorInterface
    {
        return (new InstantiatorFactory())->createInstantiator(new \ReflectionClass($class));
    }
}
