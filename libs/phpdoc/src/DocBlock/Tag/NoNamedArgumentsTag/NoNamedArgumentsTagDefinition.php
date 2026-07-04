<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\NoNamedArgumentsTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@no-named-arguments`" tag indicates that the argument names may change
 * and must not be relied upon by callers.
 *
 * ```
 * "@no-named-arguments" [ <Description> ]
 * ```
 */
final class NoNamedArgumentsTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'no-named-arguments';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): NoNamedArgumentsTag
    {
        return new NoNamedArgumentsTag(self::NAME, $description);
    }
}
