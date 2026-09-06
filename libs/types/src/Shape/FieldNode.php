<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

abstract class FieldNode extends Node
{
    public function __construct(
        public TypeNode $type,
        public bool $isOptional = false,
        public ?AttributeGroupListNode $attributes = null,
    ) {}

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
