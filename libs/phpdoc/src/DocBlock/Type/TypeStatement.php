<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Type;

use TypeLang\Type\TypeNode;

/**
 * A parsed type together with the exact source text it was read from.
 */
final readonly class TypeStatement implements \Stringable
{
    public function __construct(
        public TypeNode $type,
        /**
         * The original type text, exactly as it appeared in the source.
         *
         * @var non-empty-string
         */
        public string $source,
    ) {}

    public function __toString(): string
    {
        return $this->source;
    }
}
