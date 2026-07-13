<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests\Stub;

/**
 * Typed property without a default value, i.e. in the "uninitialized" state.
 */
final class UninitializedStub
{
    public string $uninitialized;
}
