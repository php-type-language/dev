<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

/**
 * The PHPStan platform: the "@phpstan-*" tag family understood by PHPStan.
 */
final class PhpStanPlatform extends Platform
{
    /**
     * @var non-empty-string
     */
    public const string NAME = 'PHPStan';

    public private(set) string $name = self::NAME;
}
