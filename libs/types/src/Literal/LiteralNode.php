<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

use TypeLang\Type\TypeNode;

/**
 * A value standing where a type is expected, kept both as PHP sees it
 * and as it is written.
 *
 * The two are not the same thing. A value is what PHP would compute, while
 * a raw is the very text it was computed from, quotes, radix and all.
 *
 * ```
 *  0x1F     // value: 31,     raw: "0x1F"
 *  '\x41'   // value: "\x41", raw: "'\x41'"
 * ```
 *
 * @template TValue of mixed = mixed
 */
abstract class LiteralNode extends TypeNode implements
    \Stringable
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * @var TValue
         */
        public readonly mixed $value,
        /**
         * @var non-empty-string
         */
        public readonly string $raw,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->raw;
    }
}
