<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PackageTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\Spec;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;

/**
 * The "`@package`" tag categorizes elements into a logical subdivision.
 *
 * ```
 * "@package" [ <Description> ]
 * ```
 */
final class PackageTagDefinition extends TagDefinition
{
    public const string NAME = 'package';

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

    public function create(string $name, TagPayload $result): PackageTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new PackageTag(self::NAME, $description);
    }
}
