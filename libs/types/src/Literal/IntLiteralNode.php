<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<int>
 *
 * @phpstan-consistent-constructor
 */
final class IntLiteralNode extends LiteralNode
{
    /**
     * @var numeric-string
     */
    public readonly string $decimal;

    /**
     * @param numeric-string|null $decimal
     * @param int<0, max> $offset
     */
    public function __construct(
        int $value,
        ?string $raw = null,
        ?string $decimal = null,
        int $offset = 0,
    ) {
        $this->decimal = $decimal ?? (string) $value;

        parent::__construct($value, $raw ?? (string) $value, $offset);
    }
}
