<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\OverrideTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@override`" tag marks a method as overriding an inherited definition.
 *
 * ```
 * "@override" [ <Description> ]
 * ```
 */
final class OverrideTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'override';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): OverrideTag
    {
        return new OverrideTag(self::NAME, $description);
    }
}
