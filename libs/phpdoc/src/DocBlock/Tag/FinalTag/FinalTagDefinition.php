<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\FinalTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@final`" tag declares that an element must not be overridden or
 * extended.
 *
 * ```
 * "@final" [ <Description> ]
 * ```
 */
final class FinalTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'final';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): FinalTag
    {
        return new FinalTag(self::NAME, $description);
    }
}
