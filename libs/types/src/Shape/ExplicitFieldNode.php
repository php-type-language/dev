<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\TypeNode;

/**
 * @template TKey of mixed
 *
 * @property-read string $index An alias of {@see index()} method.
 */
abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * @var list<non-empty-string>
     */
    private const VIRTUAL_PROPERTIES = [
        'index',
    ];

    public function __construct(
        /**
         * @var TKey
         */
        public mixed $key,
        TypeNode $type,
        bool $isOptional = false,
        ?AttributeGroupListNode $attributes = null,
    ) {
        parent::__construct(
            type: $type,
            isOptional: $isOptional,
            attributes: $attributes,
        );
    }

    /**
     * Gets a pretty-printed string representation of the key
     */
    abstract public function index(): string;

    public function __get(string $name): mixed
    {
        return match ($name) {
            'index' => $this->index(),
            default => throw new \OutOfRangeException(
                message: \sprintf('Undefined property %s::$%s', static::class, $name),
            ),
        };
    }

    public function __isset(string $name): bool
    {
        return \in_array($name, self::VIRTUAL_PROPERTIES, true);
    }
}
