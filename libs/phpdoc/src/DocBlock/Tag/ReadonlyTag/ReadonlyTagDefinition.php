<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ReadonlyTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TagSpecification;

/**
 * The "`@readonly`" tag declares that a property may only be written once,
 * during initialization.
 *
 * ```
 * "@readonly" [ <Description> ]
 * ```
 */
final class ReadonlyTagDefinition extends TagDefinition
{
    public const string NAME = 'readonly';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            spec: TagSpecification::maybe(
                TagSpecification::rule(DescriptionCombinator::NAME, 'description'),
            ),
            isInline: false,
        );
    }

    public function create(string $name, TagPayload $result): ReadonlyTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new ReadonlyTag(self::NAME, $description);
    }
}
