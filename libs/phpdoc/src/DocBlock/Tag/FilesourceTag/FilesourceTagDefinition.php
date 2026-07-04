<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\FilesourceTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@filesource`" tag tells documentation tooling to include the source of
 * the current file in its output.
 *
 * ```
 * "@filesource" [ <Description> ]
 * ```
 */
final class FilesourceTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'filesource';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): FilesourceTag
    {
        return new FilesourceTag(self::NAME, $description);
    }
}
