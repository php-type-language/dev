<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests\Stub;

final class TraitConsumerStub
{
    use ExampleTrait;

    public string $ownProperty = 'own-value';
}
