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
     */
    public function __construct(
        int $value,
        ?string $raw = null,
        ?string $decimal = null,
    ) {
        $this->decimal = $decimal ?? (string) $value;

        parent::__construct($value, $raw ?? (string) $value);
    }
}
