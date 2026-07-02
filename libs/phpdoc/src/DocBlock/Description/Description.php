<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Description;

final readonly class Description implements DescriptionInterface
{
    public function __construct(
        public string $value = '',
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }
}
