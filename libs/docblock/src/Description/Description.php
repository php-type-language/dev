<?php

declare(strict_types=1);

namespace TypeLang\DocBlock\Description;

final readonly class Description implements DescriptionInterface
{
    public function __construct(
        public string $value = '',
    ) {}

    public static function createFromString(string|\Stringable $description): DescriptionInterface
    {
        return match (true) {
            $description instanceof DescriptionInterface => $description,
            default => new self((string) $description),
        };
    }

    /**
     * @return ($description is null ? null : DescriptionInterface)
     */
    public static function tryCreateFromStringOrNull(string|\Stringable|null $description): ?DescriptionInterface
    {
        return match (true) {
            $description === null => null,
            $description instanceof DescriptionInterface => $description,
            default => new self((string) $description),
        };
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
