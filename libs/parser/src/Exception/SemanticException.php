<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

/**
 * Error occurring while validating the semantics of a syntactically correct
 * type statement.
 */
abstract class SemanticException extends \LogicException implements ParserExceptionInterface
{
    final public const ERROR_CODE_SHAPE_KEY_DUPLICATION = 0x01;

    final public const ERROR_CODE_SHAPE_KEY_MIX = 0x02;

    final public const ERROR_CODE_VARIADIC_WITH_DEFAULT = 0x03;

    final public const ERROR_CODE_INVALID_OPERATOR = 0x05;

    final public const ERROR_CODE_TEMPLATE_BOUND = 0x06;

    final public const ERROR_CODE_SHAPE_KEY = 0x07;

    final public const ERROR_CODE_CONST_MASK = 0x08;

    protected const CODE_LAST = self::ERROR_CODE_CONST_MASK;

    /**
     * @param int<0, max> $offset
     */
    final public function __construct(
        public readonly int $offset,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int<0, max>
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
