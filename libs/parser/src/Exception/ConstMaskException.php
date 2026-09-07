<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class ConstMaskException extends SemanticException
{
    /**
     * Occurs when a constant mask is followed by something: A mask names
     * a family of constants and is a type entire, so it takes neither
     * template arguments, nor shape fields, nor anything else.
     *
     * @param int<0, max> $offset
     */
    public static function becauseNothingFollowsAMask(int $offset = 0): self
    {
        $message = 'Constant mask is a type entire and cannot be followed by anything';

        return new self($offset, $message, self::ERROR_CODE_CONST_MASK);
    }
}
