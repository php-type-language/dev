<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\SubpackageTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@subpackage`" tag categorizes elements into a logical subdivision below
 * their package.
 *
 * ```
 * "@subpackage" [ <Description> ]
 * ```
 */
final class SubpackageTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'subpackage';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): SubpackageTag
    {
        return new SubpackageTag(self::NAME, $description);
    }
}
