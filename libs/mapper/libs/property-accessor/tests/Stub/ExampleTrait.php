<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests\Stub;

trait ExampleTrait
{
    public string $traitPublic = 'trait-public';

    private string $traitPrivate = 'trait-private';

    public function getTraitPrivate(): string
    {
        return $this->traitPrivate;
    }
}
