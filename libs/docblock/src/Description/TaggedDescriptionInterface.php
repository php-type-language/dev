<?php

declare(strict_types=1);

namespace TypeLang\DocBlock\Description;

use TypeLang\DocBlock\Tag\TagInterface;
use TypeLang\DocBlock\Tag\TagsProviderInterface;

/**
 * Any class that implements this interface is a description object
 * containing an arbitrary set of nested tags ({@see TagInterface}) and,
 * like a parent {@see DescriptionInterface}, can be represented as a string.
 *
 * @phpstan-type TaggedDescriptionComponentType DescriptionInterface|TagInterface
 *
 * @template-extends \Traversable<array-key, TaggedDescriptionComponentType>
 */
interface TaggedDescriptionInterface extends
    DescriptionInterface,
    TagsProviderInterface,
    \Traversable,
    \Countable
{
    /**
     * Gets a list of components (that is, tags of {@see TagInterface}
     * and descriptions of {@see DescriptionInterface}) that make up the
     * {@see TaggedDescriptionInterface} in the order in which these
     * elements are defined.
     *
     * @var iterable<array-key, TaggedDescriptionComponentType>
     *
     * @readonly
     */
    public iterable $components {
        get;
    }

    /**
     * Gets a list of all tags within a description
     *
     * @var iterable<array-key, TagInterface>
     */
    public iterable $tags {
        get;
    }
}
