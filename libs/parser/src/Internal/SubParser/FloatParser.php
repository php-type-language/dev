<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Literal\FloatLiteralNode;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-implements SubParserInterface<FloatLiteralNode>
 */
final class FloatParser implements SubParserInterface
{
    public function parse(string $raw, int $offset = 0): FloatLiteralNode
    {
        return new FloatLiteralNode(
            value: (float) \str_replace('_', '', $raw),
            raw: $raw,
            offset: $offset,
        );
    }
}
