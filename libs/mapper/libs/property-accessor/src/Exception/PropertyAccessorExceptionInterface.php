<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

interface PropertyAccessorExceptionInterface extends \Throwable
{
    public object $object {
        get;
    }

    public string $property {
        get;
    }
}
