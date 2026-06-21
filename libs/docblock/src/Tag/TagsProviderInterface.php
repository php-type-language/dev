<?php

declare(strict_types=1);

namespace TypeLang\DocBlock\Tag;

/**
 * Representation of any entry that contain inner tags list.
 */
interface TagsProviderInterface
{
    /**
     * Gets tags list for this object.
     *
     * @var iterable<array-key, TagInterface>
     */
    public iterable $tags {
        get;
    }
}
