<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

/**
 * Representation of any entry that contains an inner tag list.
 */
interface TagsProviderInterface
{
    /**
     * Gets a tag list for this object.
     *
     * @var iterable<array-key, TagInterface>
     */
    public iterable $tags {
        get;
    }
}
