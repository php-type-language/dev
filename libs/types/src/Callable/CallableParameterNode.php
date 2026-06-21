<?php

declare(strict_types=1);

namespace TypeLang\Type\Callable;

use TypeLang\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Literal\VariableLiteralNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

final class CallableParameterNode extends Node implements \Stringable
{
    public function __construct(
        public ?TypeNode $type = null,
        public ?VariableLiteralNode $name = null,
        public bool $output = false,
        public bool $variadic = false,
        public bool $optional = false,
        public ?AttributeGroupsListNode $attributes = null,
    ) {
        assert($type !== null || $name !== null, new \TypeError(
            'Required indication of the type or name of the parameter (one of)',
        ));

        assert($variadic === false || $optional === false, new \TypeError(
            'Parameter cannot be both variable and optional (variadic parameter is already optional)',
        ));
    }

    public function __toString(): string
    {
        $result = [];

        if ($this->output) {
            $result[] = 'output';
        }

        if ($this->variadic) {
            $result[] = 'variadic';
        }

        if ($this->optional) {
            $result[] = 'optional';
        }

        if ($result === []) {
            return 'simple';
        }

        return \implode(', ', $result);
    }
}
