<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;

final class SemanticParseException extends ParseException
{
    /**
     * Occurs when a semantic error is rebased onto the full source and
     * reported to the user with a rendered location.
     *
     * @throws SourceExceptionInterface
     */
    public static function becauseSemanticErrorOccurs(SemanticException $e, ReadableInterface $source): self
    {
        $message = \vsprintf('%s in %s %s', [
            \ucfirst($e->getMessage()),
            Formatter::source($source->content),
            Formatter::suffix($source->content, $e->getOffset()),
        ]);

        return new self($message, self::ERROR_CODE_SEMANTIC_ERROR_BASE + $e->getCode());
    }
}
