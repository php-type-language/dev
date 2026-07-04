<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PureUnlessCallableIsImpureTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TagSpecification;

/**
 * The "`@pure-unless-callable-is-impure`" tag declares a function pure unless a
 * callable it receives is itself impure.
 *
 * ```
 * "@pure-unless-callable-is-impure" [ <Description> ]
 * ```
 */
final class PureUnlessCallableIsImpureTagDefinition extends TagDefinition
{
    public const string NAME = 'pure-unless-callable-is-impure';

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

    public function create(string $name, TagPayload $result): PureUnlessCallableIsImpureTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new PureUnlessCallableIsImpureTag(self::NAME, $description);
    }
}
