<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PackageTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@package`" tag categorizes elements into a logical subdivision.
 *
 * ```
 * "@package" [ <Description> ]
 * ```
 */
final class PackageTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'package';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): PackageTag
    {
        return new PackageTag(self::NAME, $description);
    }
}
