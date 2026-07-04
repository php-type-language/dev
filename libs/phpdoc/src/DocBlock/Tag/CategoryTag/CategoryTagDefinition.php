<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\CategoryTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\Spec;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;

/**
 * The "`@category`" tag organizes groups of packages together.
 *
 * ```
 * "@category" [ <Description> ]
 * ```
 */
final class CategoryTagDefinition extends TagDefinition
{
    public const string NAME = 'category';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            spec: Spec::maybe(
                Spec::rule(DescriptionCombinator::NAME, 'description'),
            ),
            isInline: false,
        );
    }

    public function create(string $name, TagPayload $result): CategoryTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new CategoryTag(self::NAME, $description);
    }
}
