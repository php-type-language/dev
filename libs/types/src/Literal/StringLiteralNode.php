<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<string>
 *
 * @phpstan-consistent-constructor
 */
final class StringLiteralNode extends LiteralNode
{
    /**
     * @param int<0, max> $offset
     */
    final public function __construct(
        string $value,
        ?string $raw = null,
        int $offset = 0,
    ) {
        $raw ??= \sprintf("'%s'", \addcslashes($value, "'"));

        parent::__construct($value, $raw, $offset);
    }
}
