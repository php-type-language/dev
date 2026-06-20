<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<null>
 */
class NullLiteralNode extends LiteralNode
{
    public function __construct(?string $raw = null)
    {
        parent::__construct(null, $raw ?? 'null');
    }
}
