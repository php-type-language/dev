<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Tag;

use TypeLang\PhpDoc\DocBlock\Tag\InvalidTag;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\Exception\EmptyTagLineException;
use TypeLang\PhpDoc\Exception\EmptyTagNameException;
use TypeLang\PhpDoc\Exception\InvalidTagPrefixException;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

final readonly class RegexTagParser implements TagParserInterface
{
    /**
     * @var non-empty-string
     */
    private const string PATTERN_TAG = '\G@[\w\-\_\\\\:]++';

    /**
     * @var non-empty-string
     */
    private string $pattern;

    public function __construct(
        private TagFactoryInterface $factory,
    ) {
        $this->pattern = \sprintf('/^%s/isum', \addcslashes(self::PATTERN_TAG, '/'));
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

        \preg_match($this->pattern, $definition, $matches);
        $prefixedTagName = $matches[0] ?? null;

        if ($prefixedTagName === null) {
            return self::createForInvalidTagName($definition, $descriptions);
        }

        /** @var non-empty-string $tagName */
        $tagName = \substr($prefixedTagName, 1);
        $tagSuffix = \ltrim(\substr($definition, \strlen($prefixedTagName)));

        return $this->factory->create($tagName, $tagSuffix, $descriptions);
    }
}
