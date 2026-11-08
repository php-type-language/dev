<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A single word, the one that has no namespace separators in it.
 *
 * ```
 *  Some\Any::CONST_NAME
 *            ^^^^^^^^^^ a single word
 *
 *  Some\Any::BAR*BAZ
 *            ^^^ ^^^ two single words
 * ```
 */
final class Identifier extends Node implements \Stringable
{
    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of an empty value
     */
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $value,
        int $offset = 0,
    ) {
        if ($value === '') {
            throw new \InvalidArgumentException('Identifier cannot be empty');
        }

        parent::__construct($offset);
    }

    /**
     * @api
     *
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
