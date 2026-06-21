<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-extends LiteralNode<float>
 *
 * @phpstan-consistent-constructor
 */
final class FloatLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    public function __construct(
        float $value,
        ?string $raw = null,
    ) {
        parent::__construct($value, $raw ?? (string) $this->value);
    }

    public static function parse(string $value): self
    {
        if (!\is_numeric($value)) {
            return new self(0.0, $value);
        }

        return new self((float) $value, $value);
    }
}
