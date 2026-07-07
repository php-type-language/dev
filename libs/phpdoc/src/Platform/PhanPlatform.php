<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

/**
 * The Phan platform: the "@phan-*" tag family understood by Phan.
 */
final class PhanPlatform extends Platform
{
    /**
     * @var non-empty-string
     */
    public const string NAME = 'Phan';

    public private(set) string $name = self::NAME;
}
