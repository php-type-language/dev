<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests;

use PHPUnit\Framework\Attributes\Group;

#[Group('type-lang/property-accessor')]
abstract class PropertyAccessorTestCase extends TestCase
{
    /**
     * @param callable(): mixed $fn
     */
    protected static function captureThrowable(callable $fn): \Throwable
    {
        try {
            $fn();
        } catch (\Throwable $e) {
            return $e;
        }

        self::fail('Expected a throwable to be thrown, but none was');
    }
}
