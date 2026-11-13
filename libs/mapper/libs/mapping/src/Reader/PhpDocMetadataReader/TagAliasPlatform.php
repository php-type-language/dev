<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\PhpDoc\Platform\PlatformInterface;

/**
 * Declares the configured tag names as aliases of `@var`, `@param` and
 * `@return`: `@type-lang-var` is parsed as `@var`.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class TagAliasPlatform implements PlatformInterface
{
    public function __construct(
        /**
         * @var array<non-empty-lowercase-string, non-empty-lowercase-string>
         */
        private readonly array $aliases,
    ) {}

    public function getTags(): iterable
    {
        return [];
    }

    public function getAliases(): iterable
    {
        return $this->aliases;
    }

    public function getCombinators(): iterable
    {
        return [];
    }
}
