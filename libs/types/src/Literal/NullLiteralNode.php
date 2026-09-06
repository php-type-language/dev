<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<null>
 */
final class NullLiteralNode extends LiteralNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(?string $raw = null, int $offset = 0)
    {
        parent::__construct(null, $raw ?? 'null', $offset);
    }
}
