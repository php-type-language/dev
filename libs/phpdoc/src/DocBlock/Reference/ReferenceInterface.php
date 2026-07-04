<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Reference;

interface ReferenceInterface extends \Stringable
{
    /**
     * Gets {@see true} if the reference is external
     */
    public bool $isExternal {
        get;
    }
}
