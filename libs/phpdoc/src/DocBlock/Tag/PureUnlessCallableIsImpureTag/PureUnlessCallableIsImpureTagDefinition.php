<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PureUnlessCallableIsImpureTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@pure-unless-callable-is-impure`" tag declares a function pure unless a
 * callable it receives is itself impure.
 *
 * ```
 * "@pure-unless-callable-is-impure" [ <Description> ]
 * ```
 */
final class PureUnlessCallableIsImpureTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'pure-unless-callable-is-impure';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): PureUnlessCallableIsImpureTag
    {
        return new PureUnlessCallableIsImpureTag(self::NAME, $description);
    }
}
