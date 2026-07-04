<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ReadonlyTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@readonly`" tag declares that a property may only be written once,
 * during initialization.
 *
 * ```
 * "@readonly" [ <Description> ]
 * ```
 */
final class ReadonlyTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'readonly';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): ReadonlyTag
    {
        return new ReadonlyTag(self::NAME, $description);
    }
}
