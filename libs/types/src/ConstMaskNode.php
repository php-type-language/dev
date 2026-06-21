<?php

declare(strict_types=1);

namespace TypeLang\Type;

final class ConstMaskNode extends TypeNode implements \Stringable
{
    public function __construct(
        public Name $name,
    ) {}

    public function __toString(): string
    {
        return $this->name->toString() . '*';
    }
}
