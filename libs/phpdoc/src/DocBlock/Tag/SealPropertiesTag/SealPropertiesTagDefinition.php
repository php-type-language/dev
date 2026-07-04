<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\SealPropertiesTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@seal-properties`" tag forbids declaring magic properties beyond those
 * already documented.
 *
 * ```
 * "@seal-properties" [ <Description> ]
 * ```
 */
final class SealPropertiesTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'seal-properties';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): SealPropertiesTag
    {
        return new SealPropertiesTag(self::NAME, $description);
    }
}
