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
    final public function __construct(
        string $value,
        ?string $raw = null,
    ) {
        $raw ??= \sprintf('"%s"', \addcslashes($value, '"'));

        parent::__construct($value, $raw);
    }
}
