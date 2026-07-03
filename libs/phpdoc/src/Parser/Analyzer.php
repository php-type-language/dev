<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser;

use TypeLang\PhpDoc\DocBlock\DocBlock;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\Exception\ParsingExceptionInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Splitter\SplitterInterface;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;

/**
 * Groups the significant segments of a DocBlock comment into sections (a
 * description followed by tags) and builds the {@see SourceMap} for them.
 *
 * It only slices and maps the comment: parsing the description and tag
 * contents is left to the caller.
 */
final readonly class Analyzer
{
    public function __construct(
        private SplitterInterface $splitter,
        private TagParserInterface $tags,
        private DescriptionParserInterface $descriptions,
    ) {}

    public function analyze(string $docblock): DocBlock
    {
        $map = new SourceMap();

        $current = '';
        $blocks = [];

        foreach ($this->splitter->split($docblock) as $segment) {
            $text = $segment->text;
            $offset = $segment->offset;

            $map->addMapping($text, $offset);

            // A segment starting with "@" opens a new tag section, flushing
            // whatever was accumulated for the previous one.
            if (\str_starts_with($text, '@')) {
                $blocks[] = $current;
                $current = '';
            }

            $current .= $text;
        }

        $blocks[] = $current;

        return new DocBlock(
            // The first section is always the description; the rest are tags.
            description: $this->descriptions->tryParse(\array_shift($blocks)),
            tags: $this->createTags($blocks),
        );
    }

    /**
     * @param list<string> $blocks
     * @return list<TagInterface>
     * @throws ParsingExceptionInterface
     */
    private function createTags(array $blocks): array
    {
        $result = [];

        foreach ($blocks as $block) {
            $result[] = $this->tags->parse($block, $this->descriptions);
        }

        return $result;
    }
}
