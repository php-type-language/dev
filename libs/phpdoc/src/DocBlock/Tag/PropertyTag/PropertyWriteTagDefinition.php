<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PropertyTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Type\TypeStatement;

/**
 * The "`@property-write`" tag declares a magic property that can only be
 * written.
 *
 * ```
 * "@property-write" <Type> <Variable> [ <Description> ]
 * ```
 */
final class PropertyWriteTagDefinition extends MagicPropertyTagDefinition
{
    public const string NAME = 'property-write';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(
        TypeStatement $type,
        string $variable,
        ?DescriptionInterface $description,
    ): PropertyWriteTag {
        return new PropertyWriteTag(self::NAME, $type, $variable, $description);
    }
}
