<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\PropertyAccessor\Exception\InvalidPropertyValueException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAccessException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAccessorExceptionInterface;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyAlreadyInitializedException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;
use TypeLang\Mapper\PropertyAccessor\Exception\UninitializedPropertyException;
use TypeLang\Mapper\PropertyAccessor\Tests\PropertyAccessorTestCase;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\VisibilityStub;

final class PropertyAccessExceptionTest extends PropertyAccessorTestCase
{
    /**
     * @return iterable<non-empty-string, array{\Closure(object, string): PropertyAccessException, non-empty-string}>
     */
    public static function exceptionDataProvider(): iterable
    {
        yield 'property does not exist' => [
            static fn(object $o, string $p) => PropertyNotFoundException::becausePropertyDoesNotExist($o, $p),
            'Property %s::$prop does not exist',
        ];
        yield 'property is static' => [
            static fn(object $o, string $p) => PropertyNotFoundException::becausePropertyIsStatic($o, $p),
            'Property %s::$prop is static',
        ];
        yield 'property is write-only' => [
            static fn(object $o, string $p) => PropertyNotReadableException::becausePropertyIsWriteOnly($o, $p),
            'Property %s::$prop is write-only',
        ];
        yield 'reading is disabled' => [
            static fn(object $o, string $p) => PropertyNotReadableException::becauseReadingIsDisabled($o, $p),
            'Reading of property %s::$prop is disabled',
        ];
        yield 'reading failed' => [
            static fn(object $o, string $p) => PropertyNotReadableException::becauseReadingFailed($o, $p),
            'Property %s::$prop cannot be read',
        ];
        yield 'property is read-only' => [
            static fn(object $o, string $p) => PropertyNotWritableException::becausePropertyIsReadOnly($o, $p),
            'Property %s::$prop is read-only',
        ];
        yield 'writing is disabled' => [
            static fn(object $o, string $p) => PropertyNotWritableException::becauseWritingIsDisabled($o, $p),
            'Writing of property %s::$prop is disabled',
        ];
        yield 'writing failed' => [
            static fn(object $o, string $p) => PropertyNotWritableException::becauseWritingFailed($o, $p),
            'Property %s::$prop cannot be written',
        ];
        yield 'property is not initialized' => [
            static fn(object $o, string $p) => UninitializedPropertyException::becausePropertyIsNotInitialized($o, $p),
            'Property %s::$prop is not initialized',
        ];
        yield 'readonly property is initialized' => [
            static fn(object $o, string $p) => PropertyAlreadyInitializedException
                ::becausePropertyIsAlreadyInitialized($o, $p),
            'Readonly property %s::$prop is already initialized',
        ];
        yield 'value type is invalid' => [
            static fn(object $o, string $p) => InvalidPropertyValueException
                ::becauseValueTypeIsInvalid($o, $p, 'int', 'str'),
            'Property %s::$prop of type int cannot be assigned a value of type string',
        ];
    }

    /**
     * @param \Closure(object, string): PropertyAccessException $create
     */
    #[DataProvider('exceptionDataProvider')]
    public function testExceptionCapturesContextAndDescribesFailure(\Closure $create, string $message): void
    {
        $object = new VisibilityStub();

        $error = $create($object, 'prop');

        self::assertInstanceOf(PropertyAccessorExceptionInterface::class, $error);
        self::assertSame($object, $error->object);
        self::assertSame('prop', $error->property);
        self::assertSame(\sprintf($message, VisibilityStub::class), $error->getMessage());
    }

    public function testFailureIncludesMessageOfPreviousThrowable(): void
    {
        $previous = new \RuntimeException('root cause');

        $read = PropertyNotReadableException::becauseReadingFailed(new VisibilityStub(), 'prop', $previous);
        $write = PropertyNotWritableException::becauseWritingFailed(new VisibilityStub(), 'prop', $previous);

        self::assertSame($previous, $read->getPrevious());
        self::assertSame($previous, $write->getPrevious());
        self::assertStringEndsWith(': root cause', $read->getMessage());
        self::assertStringEndsWith(': root cause', $write->getMessage());
    }
}
