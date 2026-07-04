<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\CategoryTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@category`" tag organizes groups of packages together.
 *
 * ```
 * "@category" [ <Description> ]
 * ```
 */
final class CategoryTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'category';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): CategoryTag
    {
        return new CategoryTag(self::NAME, $description);
    }
}
