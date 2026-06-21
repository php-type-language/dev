<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Literal;

/**
 * @template-extends LiteralNode<non-empty-string>
 *
 * @phpstan-consistent-constructor
 */
class VariableLiteralNode extends LiteralNode implements ParsableLiteralNodeInterface
{
    /**
     * @param non-empty-string $value
     */
    public function __construct(string $value)
    {
        if (\strlen($value) < 2) {
            throw new \InvalidArgumentException('Variable name length must be greater than 1');
        }

        if (!\str_starts_with($value, '$')) {
            throw new \InvalidArgumentException('Variable name must start with "$" character');
        }

        /** @var non-empty-string $normalized */
        $normalized = \substr($value, 1);

        parent::__construct($normalized, $value);
    }

    public static function parse(string $value): static
    {
        if (!\str_starts_with($value, '$')) {
            $value = '$' . $value;
        }

        return new static($value);
    }
}
