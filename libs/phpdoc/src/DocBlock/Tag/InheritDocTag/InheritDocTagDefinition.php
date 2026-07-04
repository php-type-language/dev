<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\InheritDocTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@inheritdoc`" tag reuses the documentation of the parent element.
 *
 * ```
 * "@inheritdoc" [ <Description> ]
 * ```
 */
final class InheritDocTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'inheritdoc';

    public function __construct()
    {
        parent::__construct(self::NAME, isInline: true);
    }

    protected function make(?DescriptionInterface $description): InheritDocTag
    {
        return new InheritDocTag(self::NAME, $description);
    }
}
