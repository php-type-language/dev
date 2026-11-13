<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Tests;

use PHPUnit\Framework\Attributes\RequiresSetting;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Instantiator\Tests\Stub\ConstructorStub;
use TypeLang\Mapper\Instantiator\Tests\Stub\EmptyStub;

final class WithoutConstructorInstantiatorTest extends TestCase
{
    public function testDoesNotCallConstructor(): void
    {
        $object = (new InstantiatorFactory())
            ->createWithoutConstructor(ConstructorStub::class)
            ->instantiate();

        self::assertInstanceOf(ConstructorStub::class, $object);
        self::assertFalse($object->isConstructed);
    }

    public function testInitializesDefaultPropertyValues(): void
    {
        $object = (new InstantiatorFactory())
            ->createWithoutConstructor(EmptyStub::class)
            ->instantiate();

        self::assertSame('default', $object->value);
    }

    public function testCreatesDistinctObjects(): void
    {
        $instantiator = (new InstantiatorFactory())->createWithoutConstructor(EmptyStub::class);

        $first = $instantiator->instantiate();
        $first->value = 'changed';

        $second = $instantiator->instantiate();

        self::assertNotSame($first, $second);
        self::assertSame('default', $second->value);
    }

    #[RequiresSetting('zend.assertions', '1')]
    public function testRejectsArguments(): void
    {
        $instantiator = (new InstantiatorFactory())->createWithoutConstructor(EmptyStub::class);

        $this->expectException(\AssertionError::class);

        $instantiator->instantiate(['value' => 'some']);
    }
}
