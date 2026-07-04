<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\InternalTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@internal`" tag marks an element as internal to its package, or, when
 * used inline, documents information meant only for that package's maintainers.
 *
 * ```
 * "@internal" [ <Description> ]
 * ```
 */
final class InternalTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'internal';

    public function __construct()
    {
        parent::__construct(self::NAME, isInline: true);
    }

    protected function make(?DescriptionInterface $description): InternalTag
    {
        return new InternalTag(self::NAME, $description);
    }
}
