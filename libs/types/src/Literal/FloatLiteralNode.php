<?php

declare(strict_types=1);

namespace TypeLang\Node\Literal;

/**
 * @template-extends LiteralNode<float>
 *
 * @phpstan-consistent-constructor
 */
class FloatLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    public function __construct(
        float $value,
        ?string $raw = null,
    ) {
        parent::__construct($value, $raw ?? (string) $this->value);
    }

    public static function parse(string $value): static
    {
        if (!\is_numeric($value)) {
            return new static(0.0, $value);
        }

        return new static((float) $value, $value);
    }
}
