<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

/**
 * Checks that a string is built only from an allowed set of characters, such as
 * the characters permitted in a member name or a namespaced class name.
 */
final readonly class NameValidator
{
    /**
     * The ASCII characters allowed in a method, constant, property or variable
     * name.
     */
    private const string NAME_CHARS = 'abcdefghijklmnopqrstuvwxyz'
        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        . '0123456789'
        . '_';

    /**
     * The ASCII characters that are not allowed; a value built only from allowed
     * characters (any byte above 0x7F included) contains none of them.
     */
    private string $terminators;

    /**
     * @param string $allowed additional ASCII characters permitted on top of
     *        the base name characters (e.g. "\\" for a namespaced class name)
     */
    public function __construct(string $allowed = '')
    {
        $this->terminators = self::terminatorsExcept(self::NAME_CHARS . $allowed);
    }

    /**
     * Returns $value when it consists solely of the allowed characters,
     * otherwise null.
     *
     * @return non-empty-string|null
     */
    public function validate(string $value): ?string
    {
        return $value !== '' && \strcspn($value, $this->terminators) === \strlen($value)
            ? $value
            : null;
    }

    private static function terminatorsExcept(string $allowed): string
    {
        $mask = '';

        for ($byte = 0x00; $byte <= 0x7F; ++$byte) {
            $char = \chr($byte);

            if (!\str_contains($allowed, $char)) {
                $mask .= $char;
            }
        }

        return $mask;
    }
}
