<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\TypeNode;

/**
 * @template TKey of mixed
 */
abstract class ExplicitFieldNode extends FieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * @var TKey
         */
        public mixed $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct(
            type: $type,
            isOptional: $isOptional,
            offset: $offset,
        );
    }

    /**
     * Gets a pretty-printed string representation of the key
     */
    abstract public function getIndex(): string;
}
