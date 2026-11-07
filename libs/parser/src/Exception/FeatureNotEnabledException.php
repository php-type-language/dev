<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Lexer\Token\Token;

/**
 * The source uses a syntax of the feature that the parser has disabled.
 *
 * ```
 *  $parser = new TypeParser(new TypeParserFeatures(literals: false));
 *  $parser->parse('42');
 *  // => FeatureNotEnabledException: Literals not allowed
 * ```
 *
 * @see \TypeLang\Parser\TypeParserFeatures
 */
final class FeatureNotEnabledException extends ParsingException
{
    /**
     * @param non-empty-string $feature The name of the disabled feature
     * @param non-empty-string $text The part of the source that uses it
     * @param int<0, max> $offset The offset of this part in bytes
     */
    public static function becauseFeatureIsNotEnabled(
        string $feature,
        string $text,
        int $offset,
        ReadableInterface $source,
    ): self {
        $token = new Token(
            id: -1,
            name: null,
            channel: Channel::Default,
            value: $text,
            offset: $offset,
        );

        $message = \sprintf('%s not allowed', \ucfirst($feature));

        return new self($token, $source, $message);
    }
}
