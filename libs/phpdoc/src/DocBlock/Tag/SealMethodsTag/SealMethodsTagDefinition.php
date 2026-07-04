<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\SealMethodsTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@seal-methods`" tag forbids declaring magic methods beyond those
 * already documented.
 *
 * ```
 * "@seal-methods" [ <Description> ]
 * ```
 */
final class SealMethodsTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'seal-methods';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): SealMethodsTag
    {
        return new SealMethodsTag(self::NAME, $description);
    }
}
