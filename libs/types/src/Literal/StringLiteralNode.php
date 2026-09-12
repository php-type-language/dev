<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A string, kept both decoded and as it is written.
 *
 * ```
 *  "\x41\x42"  // value: "AB", raw: "\"\x41\x42\""
 * ```
 *
 * @template-extends ScalarNode<string>
 *
 * @phpstan-consistent-constructor
 */
final class StringLiteralNode extends ScalarNode
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
