<?php

declare(strict_types=1);

namespace TypeLang\Type\Callable;

use TypeLang\Type\Literal\VariableLiteralNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

final class CallableParameterNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public ?TypeNode $type = null,
        public ?VariableLiteralNode $name = null,
        public bool $isOutput = false,
        public bool $isVariadic = false,
        public bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct($offset);

        \assert($type !== null || $name !== null, new \TypeError(
            'Required indication of the type or name of the parameter (one of)',
        ));

        \assert($isVariadic === false || $isOptional === false, new \TypeError(
            'Parameter cannot be both variable and optional (variadic parameter is already optional)',
        ));
    }

    /**
     * Returns {@see true} in case of the parameter is an instance of the
     * passed class.
     *
     * @param class-string $class
     */
    public function is(string $class): bool
    {
        return $this instanceof $class;
    }
}
