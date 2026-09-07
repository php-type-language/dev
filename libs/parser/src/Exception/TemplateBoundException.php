<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

final class TemplateBoundException extends SemanticException
{
    /**
     * Occurs when a template parameter is bounded with a word that bounds
     * nothing.
     *
     * @param non-empty-string $operator
     * @param int<0, max> $offset
     */
    public static function becauseOperatorIsUnknown(string $operator, int $offset = 0): self
    {
        $message = \sprintf(
            'Template parameter cannot be bounded with "%s", expected one of "of", "as" or "super"',
            $operator,
        );

        return new self($offset, $message, self::ERROR_CODE_TEMPLATE_BOUND);
    }

    /**
     * Occurs when a template parameter is written with the same kind of
     * limit more than once.
     *
     * @param non-empty-string $kind
     * @param int<0, max> $offset
     */
    public static function becauseBoundIsDuplicated(string $kind, int $offset = 0): self
    {
        $message = \sprintf('Template parameter cannot have more than one %s', $kind);

        return new self($offset, $message, self::ERROR_CODE_TEMPLATE_BOUND);
    }

    /**
     * Occurs when a bound is written behind a default, where it reads as
     * a bound of the default itself.
     *
     * @param int<0, max> $offset
     */
    public static function becauseDefaultIsNotWrittenLast(int $offset = 0): self
    {
        $message = 'Template parameter default must be written last, since a bound '
            . 'behind it reads as a bound of the default itself';

        return new self($offset, $message, self::ERROR_CODE_TEMPLATE_BOUND);
    }
}
