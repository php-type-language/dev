<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Reference;

abstract readonly class CodeReference implements ReferenceInterface
{
    public bool $isExternal;

    public function __construct()
    {
        $this->isExternal = false;
    }
}
