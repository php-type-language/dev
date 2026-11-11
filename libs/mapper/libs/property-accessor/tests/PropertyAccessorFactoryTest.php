<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\RequiresPhp;
use TypeLang\Mapper\PropertyAccessor\DynamicPropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\ModernReflectionPropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;
use TypeLang\Mapper\PropertyAccessor\ReflectionPropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\Tests\Stub\VisibilityStub;

final class PropertyAccessorFactoryTest extends PropertyAccessorTestCase
{
    #[RequiresPhp('< 8.4.0')]
    public function testCreatesReflectionAccessorBeforePhp84(): void
    {
        $accessor = (new PropertyAccessorFactory())->create(new VisibilityStub());

        self::assertInstanceOf(ReflectionPropertyAccessor::class, $accessor);
    }

    #[RequiresPhp('>= 8.4.0')]
    public function testCreatesModernAccessorOnPhp84(): void
    {
        $accessor = (new PropertyAccessorFactory())->create(new VisibilityStub());

        self::assertInstanceOf(ModernReflectionPropertyAccessor::class, $accessor);
    }

    public function testCreatesDynamicAccessorForStdClass(): void
    {
        $accessor = (new PropertyAccessorFactory())->create(new \stdClass());

        self::assertInstanceOf(DynamicPropertyAccessor::class, $accessor);
    }

    public function testAccessorsOfSameClassAreBoundToTheirOwnObjects(): void
    {
        $factory = new PropertyAccessorFactory();

        $first = new VisibilityStub();
        $second = new VisibilityStub();

        $firstAccessor = $factory->create($first);
        $secondAccessor = $factory->create($second);

        $firstAccessor->write('privateProperty', 'first');
        $secondAccessor->write('privateProperty', 'second');

        self::assertNotSame($firstAccessor, $secondAccessor);
        self::assertSame('first', $first->getPrivateProperty());
        self::assertSame('second', $second->getPrivateProperty());
    }
}
