<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Condition\Condition;

/**
 * A type chosen by a condition.
 *
 * ```
 *  ($value is int ? string : bool)
 *   ^^^^^^^^^^^^^                  the condition
 *                   ^^^^^^         the type it holds for
 *                            ^^^^  the type it does not
 * ```
 */
final class TernaryExpressionNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Condition $condition,
        public TypeNode $then,
        public TypeNode $else,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
