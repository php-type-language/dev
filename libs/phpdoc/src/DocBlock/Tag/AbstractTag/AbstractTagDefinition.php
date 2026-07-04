<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\AbstractTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@abstract`" tag declares an element as abstract.
 *
 * ```
 * "@abstract" [ <Description> ]
 * ```
 */
final class AbstractTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'abstract';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): AbstractTag
    {
        return new AbstractTag(self::NAME, $description);
    }
}
