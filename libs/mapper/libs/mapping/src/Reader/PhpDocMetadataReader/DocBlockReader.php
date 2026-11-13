<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\PhpDoc\DocBlock\DocBlock;
use TypeLang\PhpDoc\DocBlock\Tag\InvalidTag;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\DocBlock\Tag\TypedTag;
use TypeLang\PhpDoc\DocBlock\Tag\VariableTagInterface;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTag;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\Exception\PhpDocExceptionInterface;

/**
 * Finds typed tags in docblocks.
 *
 * A lookup returns the found tag, the reason the tag or the docblock cannot
 * be read, or {@see null} in case of there is no tag.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class DocBlockReader
{
    /**
     * @var array<string, DocBlock|PhpDocExceptionInterface>
     */
    private array $blocks = [];

    public function __construct(
        private readonly DocBlockParser $parser,
    ) {}

    /**
     * Returns the first typed or unparsable tag by the order of the names,
     * a tag naming a variable other than the given one is skipped.
     *
     * @param list<non-empty-lowercase-string> $names
     */
    public function findTag(string|false $docComment, array $names, string $variable): TypedTag|\Throwable|null
    {
        $block = $this->parse($docComment);

        if (!$block instanceof DocBlock) {
            return $block;
        }

        foreach ($names as $name) {
            foreach ($block as $tag) {
                if (\strtolower($tag->name) !== $name) {
                    continue;
                }

                if ($tag instanceof InvalidTag) {
                    return $tag->reason;
                }

                if ($tag instanceof TypedTag && (self::findVariable($tag) ?? $variable) === $variable) {
                    return $tag;
                }
            }
        }

        return null;
    }

    /**
     * Returns the first typed tag naming the variable by the order of the
     * names, or the first unparsable tag in case of there is none.
     *
     * @param list<non-empty-lowercase-string> $names
     */
    public function findVariableTag(string|false $docComment, array $names, string $variable): TypedTag|\Throwable|null
    {
        $block = $this->parse($docComment);

        if (!$block instanceof DocBlock) {
            return $block;
        }

        $invalid = null;

        foreach ($names as $name) {
            foreach ($block as $tag) {
                if (\strtolower($tag->name) !== $name) {
                    continue;
                }

                if ($tag instanceof InvalidTag) {
                    $invalid ??= $tag->reason;
                }

                if ($tag instanceof TypedTag && self::findVariable($tag) === $variable) {
                    return $tag;
                }
            }
        }

        return $invalid;
    }

    private function parse(string|false $docComment): DocBlock|PhpDocExceptionInterface|null
    {
        if ($docComment === false) {
            return null;
        }

        return $this->blocks[$docComment] ??= $this->tryParse($docComment);
    }

    private function tryParse(string $docComment): DocBlock|PhpDocExceptionInterface
    {
        try {
            return $this->parser->parse($docComment);
        } catch (PhpDocExceptionInterface $e) {
            return $e;
        }
    }

    private static function findVariable(TagInterface $tag): ?string
    {
        if ($tag instanceof VarTag || $tag instanceof VariableTagInterface) {
            return $tag->variable;
        }

        return null;
    }
}
