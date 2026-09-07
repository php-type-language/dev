<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class ShapeKeyException extends SemanticException
{
    /**
     * Occurs when a shape field is written with a key that is no key: A type
     * is read the way a constant reference is, and only some of what it may
     * be names a field.
     *
     * @param int<0, max> $offset
     */
    public static function becauseKeyIsNotAName(int $offset = 0): self
    {
        $message = 'Shape key must be a name, a number, a string or a reference to a constant';

        return new self($offset, $message, self::ERROR_CODE_SHAPE_KEY);
    }
}
