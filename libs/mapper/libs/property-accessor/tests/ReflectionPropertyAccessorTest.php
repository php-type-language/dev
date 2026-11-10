<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Tests;

use TypeLang\Mapper\PropertyAccessor\PropertyAccessor;
use TypeLang\Mapper\PropertyAccessor\ReflectionPropertyAccessor;

final class ReflectionPropertyAccessorTest extends ReflectionPropertyAccessorTestCase
{
    protected static function createPrototype(\ReflectionClass $class): PropertyAccessor
    {
        return new ReflectionPropertyAccessor($class);
    }
}
