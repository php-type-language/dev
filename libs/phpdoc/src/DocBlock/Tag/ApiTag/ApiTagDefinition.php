<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ApiTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@api`" tag marks an element as part of the public, supported API of its
 * package.
 *
 * ```
 * "@api" [ <Description> ]
 * ```
 */
final class ApiTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'api';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): ApiTag
    {
        return new ApiTag(self::NAME, $description);
    }
}
