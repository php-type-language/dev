<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests\Exception;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Instantiator\Exception\ClassNotFoundException;
use TypeLang\Mapper\Instantiator\Exception\ClassNotInstantiableException;
use TypeLang\Mapper\Instantiator\Exception\ConstructionFailedException;
use TypeLang\Mapper\Instantiator\Exception\InstantiationException;
use TypeLang\Mapper\Instantiator\Exception\InstantiatorExceptionInterface;
use TypeLang\Mapper\Instantiator\Exception\MethodNotCallableException;
use TypeLang\Mapper\Instantiator\Tests\TestCase;

final class InstantiationExceptionTest extends TestCase
{
    /**
     * @var non-empty-string
     */
    private const CLASS_NAME = 'Some\\"Any"';

    /**
     * @return iterable<non-empty-string, array{
     *     \Closure(string): InstantiationException,
     *     non-empty-string,
     * }>
     */
    public static function exceptionDataProvider(): iterable
    {
        yield 'class does not exist' => [
            static fn(string $c) => ClassNotFoundException::becauseClassDoesNotExist($c),
            'Class "Some\\\\"Any\\"" does not exist',
        ];
        yield 'class is abstract' => [
            static fn(string $c) => ClassNotInstantiableException::becauseClassIsAbstract($c),
            'Cannot instantiate abstract class "Some\\\\"Any\\""',
        ];
        yield 'class is interface' => [
            static fn(string $c) => ClassNotInstantiableException::becauseClassIsInterface($c),
            'Cannot instantiate interface "Some\\\\"Any\\""',
        ];
        yield 'class is trait' => [
            static fn(string $c) => ClassNotInstantiableException::becauseClassIsTrait($c),
            'Cannot instantiate trait "Some\\\\"Any\\""',
        ];
        yield 'class is enum' => [
            static fn(string $c) => ClassNotInstantiableException::becauseClassIsEnum($c),
            'Cannot instantiate enum "Some\\\\"Any\\""',
        ];
        yield 'class is internal final' => [
            static fn(string $c) => ClassNotInstantiableException::becauseClassIsInternalFinal($c),
            'Cannot instantiate internal final class "Some\\\\"Any\\"" without the constructor',
        ];
        yield 'constructor is not public' => [
            static fn(string $c) => MethodNotCallableException::becauseConstructorIsNotPublic($c),
            'Constructor of class "Some\\\\"Any\\"" is not public',
        ];
        yield 'method does not exist' => [
            static fn(string $c) => MethodNotCallableException::becauseMethodDoesNotExist($c, 'create'),
            'Method "Some\\\\"Any\\"::create()" does not exist',
        ];
        yield 'method is not public' => [
            static fn(string $c) => MethodNotCallableException::becauseMethodIsNotPublic($c, 'create'),
            'Method "Some\\\\"Any\\"::create()" is not public',
        ];
        yield 'method is not static' => [
            static fn(string $c) => MethodNotCallableException::becauseMethodIsNotStatic($c, 'create'),
            'Method "Some\\\\"Any\\"::create()" is not static',
        ];
        yield 'method is abstract' => [
            static fn(string $c) => MethodNotCallableException::becauseMethodIsAbstract($c, 'create'),
            'Method "Some\\\\"Any\\"::create()" is abstract',
        ];
        yield 'constructor has thrown' => [
            static fn(string $c) => ConstructionFailedException::becauseConstructorHasThrown(
                $c,
                new \DomainException('reason'),
            ),
            'Constructor of class "Some\\\\"Any\\"" has thrown an exception: reason',
        ];
        yield 'method has thrown' => [
            static fn(string $c) => ConstructionFailedException::becauseMethodHasThrown(
                $c,
                'create',
                new \DomainException('reason'),
            ),
            'Method "Some\\\\"Any\\"::create()" has thrown an exception: reason',
        ];
        yield 'method returned invalid value' => [
            static fn(string $c) => ConstructionFailedException::becauseMethodReturnedInvalidValue($c, 'create', null),
            'Method "Some\\\\"Any\\"::create()" must return an instance of "Some\\\\"Any\\"", null returned',
        ];
    }

    /**
     * @param \Closure(string): InstantiationException $create
     */
    #[DataProvider('exceptionDataProvider')]
    public function testExceptionCapturesClassAndDescribesFailure(\Closure $create, string $message): void
    {
        $error = $create(self::CLASS_NAME);

        self::assertInstanceOf(InstantiatorExceptionInterface::class, $error);
        self::assertSame(self::CLASS_NAME, $error->class);
        self::assertSame($message, $error->getMessage());
    }

    public function testExceptionKeepsPreviousThrowable(): void
    {
        $previous = new \DomainException('reason');

        self::assertSame(
            $previous,
            ConstructionFailedException::becauseConstructorHasThrown('Some', $previous)->getPrevious(),
        );
        self::assertSame(
            $previous,
            ConstructionFailedException::becauseMethodHasThrown('Some', 'create', $previous)->getPrevious(),
        );
    }
}
