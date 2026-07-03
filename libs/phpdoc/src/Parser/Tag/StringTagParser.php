<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Tag;

use TypeLang\PhpDoc\DocBlock\Tag\InvalidTag;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\Exception\EmptyTagLineException;
use TypeLang\PhpDoc\Exception\EmptyTagNameException;
use TypeLang\PhpDoc\Exception\InvalidTagPrefixException;
use TypeLang\PhpDoc\Exception\ParsingException;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * Parses a tag definition into a {@see TagInterface}.
 *
 * A definition is an "@" followed by a name and an optional, whitespace
 * separated description:
 *
 * ```
 *
 * @name
 * @name the description text
 * ```
 *
 * A definition that does not start with an "@", or whose "@" is not followed
 * by a name, is an {@see InvalidTag}.
 */
final readonly class StringTagParser implements TagParserInterface
{
    /**
     * The ASCII characters allowed inside a tag name.
     *
     * @var non-empty-string
     */
    private const string ASCII_NAME_CHARS = 'abcdefghijklmnopqrstuvwxyz'
        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        . '0123456789'
        . '_-\\:';

    private string $nameTerminators;

    public function __construct(
        private TagFactoryInterface $factory,
    ) {
        $this->nameTerminators = self::createTerminatorMask();
    }

    private static function createTerminatorMask(): string
    {
        $mask = '';

        for ($byte = 0x00; $byte <= 0x7F; ++$byte) {
            $char = \chr($byte);

            if (!\str_contains(self::ASCII_NAME_CHARS, $char)) {
                $mask .= $char;
            }
        }

        return $mask;
    }

    private static function createForEmptyTagLine(): InvalidTag
    {
        $reason = EmptyTagLineException::becauseTagLineIsEmpty();

        return new InvalidTag($reason);
    }

    private static function createForInvalidTagPrefix(
        string $definition,
        DescriptionParserInterface $descriptions,
    ): InvalidTag {
        $reason = InvalidTagPrefixException::becauseTagPrefixIsInvalid($definition);
        $description = $descriptions->tryParse($definition);

        return new InvalidTag($reason, description: $description);
    }

    private static function createForInvalidTagName(
        string $definition,
        DescriptionParserInterface $descriptions,
    ): InvalidTag {
        $reason = EmptyTagNameException::becauseTagNameIsEmpty($definition);
        $description = $descriptions->tryParse(\substr($definition, 1));

        return new InvalidTag($reason, description: $description);
    }

    public function parse(string $definition, DescriptionParserInterface $descriptions): TagInterface
    {
        if ($definition === '') {
            return self::createForEmptyTagLine();
        }

        if ($definition[0] !== '@') {
            return self::createForInvalidTagPrefix($definition, $descriptions);
        }

        $length = \strcspn($definition, $this->nameTerminators, 1);
        $name = \substr($definition, 1, $length);

        if ($name === '') {
            return self::createForInvalidTagName($definition, $descriptions);
        }

        $suffix = \ltrim(\substr($definition, 1 + $length));

        try {
            return $this->factory->create($name, $suffix, $descriptions);
        } catch (ParsingException $e) {
            throw $e->withSource($definition, $e->offset + $length + 1);
        }
    }
}
