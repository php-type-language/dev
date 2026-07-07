<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

/**
 * The Psalm platform: the "@psalm-*" tag family understood by Psalm.
 */
final class PsalmPlatform extends Platform
{
    /**
     * @var non-empty-string
     */
    public const string NAME = 'Psalm';

    public private(set) string $name = self::NAME;
}
