<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Literal;

/**
 * @template-extends LiteralNode<bool>
 *
 * @phpstan-consistent-constructor
 */
class BoolLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    public function __construct(
        bool $value,
        ?string $raw = null,
    ) {
        parent::__construct($value, $raw ?? ($value ? 'true' : 'false'));
    }

    public static function parse(string $value): static
    {
        return new static(\strtolower($value) === 'true', $value);
    }
}
