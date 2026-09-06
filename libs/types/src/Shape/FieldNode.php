<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

abstract class FieldNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $type,
        public bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    /**
     * Returns {@see true} in case of the field is an instance of the
     * passed class.
     *
     * @param class-string $class
     */
    public function is(string $class): bool
    {
        return $this instanceof $class;
    }
}
