<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * Failure of reading or writing a property of an object.
 *
 * @template TSubject of object = object
 *
 * @property-read TSubject $object
 * @property-read string $property
 */
interface PropertyAccessorExceptionInterface extends \Throwable {}
