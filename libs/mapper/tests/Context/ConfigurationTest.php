<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Tests\Context;

use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Tests\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testStrictnessIsOverriddenInCopy(): void
    {
        $config = new Configuration(strict: false);
        $strict = $config->withStrict(true);

        self::assertNotSame($config, $strict);
        self::assertFalse($config->strict);
        self::assertTrue($strict->strict);
    }
}
