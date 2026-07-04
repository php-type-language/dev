<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\OverrideTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\Spec;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;

/**
 * The "`@override`" tag marks a method as overriding an inherited definition.
 *
 * ```
 * "@override" [ <Description> ]
 * ```
 */
final class OverrideTagDefinition extends TagDefinition
{
    public const string NAME = 'override';

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

    public function create(string $name, TagPayload $result): OverrideTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new OverrideTag(self::NAME, $description);
    }
}
