<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('unit'), Group('type-lang/types')]
abstract class TestCase extends BaseTestCase
{
    protected static function skipWhenAssertsAreDisabled(): void
    {
        if (self::isAssertsAreEnabled()) {
            return;
        }

        self::markTestIncomplete('PHP assertions are disabled');
    }

    protected static function isAssertsAreDisabled(): bool
    {
        return !self::isAssertsAreEnabled();
    }

    protected static function isAssertsAreEnabled(): bool
    {
        $enabled = false;

        \assert($enabled = true);

        return $enabled;
    }
}
