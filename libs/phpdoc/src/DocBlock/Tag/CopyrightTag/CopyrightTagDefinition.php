<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\CopyrightTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@copyright`" tag documents the copyright information of an element.
 *
 * ```
 * "@copyright" [ <Description> ]
 * ```
 */
final class CopyrightTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'copyright';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): CopyrightTag
    {
        return new CopyrightTag(self::NAME, $description);
    }
}
