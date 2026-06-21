<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

/**
 * Representation of invalid tag definition.
 */
interface InvalidTagInterface extends TagInterface
{
    /**
     * Gets the reason why this tag is invalid.
     */
    public \Throwable $reason {
        get;
    }
}
