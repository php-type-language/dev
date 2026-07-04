<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\TodoTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FlagTagDefinition;

/**
 * The "`@todo`" tag records a task that still needs to be done for an element.
 *
 * ```
 * "@todo" [ <Description> ]
 * ```
 */
final class TodoTagDefinition extends FlagTagDefinition
{
    public const string NAME = 'todo';

    public function __construct()
    {
        parent::__construct(self::NAME);
    }

    protected function make(?DescriptionInterface $description): TodoTag
    {
        return new TodoTag(self::NAME, $description);
    }
}
