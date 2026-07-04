<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\IgnoreTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@ignore`" tag tells documentation tooling to skip the element it is
 * attached to.
 *
 * ```
 * "@ignore" [ <Description> ]
 * ```
 */
final class IgnoreTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'ignore';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): IgnoreTag
    {
        return new IgnoreTag(self::NAME, $description);
    }
}
