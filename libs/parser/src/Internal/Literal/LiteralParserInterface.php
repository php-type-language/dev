<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\Literal;

use TypeLang\Type\Literal\LiteralNode;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-covariant TResult of LiteralNode
 */
interface LiteralParserInterface
{
    /**
     * @param non-empty-string $raw
     * @param int<0, max> $offset
     * @return TResult
     */
    public function parse(string $raw, int $offset = 0): LiteralNode;
}
