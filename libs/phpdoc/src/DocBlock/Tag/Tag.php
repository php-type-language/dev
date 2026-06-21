<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\DocBlock\Description\Description;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;

abstract class Tag implements TagInterface
{
    public readonly ?DescriptionInterface $description;

    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        \Stringable|string|null $description = null,
    ) {
        $this->description = Description::tryCreateFromStringOrNull($description);
    }

    public function __toString(): string
    {
        if ($this->description === null) {
            return \sprintf('@%s', $this->name);
        }

        return \rtrim(\vsprintf('@%s %s', [
            $this->name,
            (string) $this->description,
        ]));
    }
}
