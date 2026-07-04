<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Reference;

final readonly class SymbolReference extends CodeReference
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
    ) {
        parent::__construct();
    }

    public function __toString(): string
    {
        return \sprintf('%s', $this->name);
    }
}
