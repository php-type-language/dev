<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc;

use JetBrains\PhpStorm\Language;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\DocBlock;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\Exception\ParsingExceptionInterface;
use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\SourceMap;
use TypeLang\PhpDoc\Parser\Splitter\SplitterInterface;
use TypeLang\PhpDoc\Parser\Splitter\StringSplitter;
use TypeLang\PhpDoc\Parser\Tag\StringTagParser;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;

final readonly class DocBlockParser implements DocBlockParserInterface
{
    private SplitterInterface $splitter;
    private TagFactoryInterface $factory;
    private TagParserInterface $tags;
    private DescriptionParserInterface $descriptions;

    public function __construct()
    {
        $this->splitter = $this->createDocBlockSplitter();
        $this->factory = $this->createTagFactory();
        $this->tags = $this->createTagParser($this->factory);
        $this->descriptions = $this->createDescriptionParser($this->tags);
    }

    private function createDocBlockSplitter(): SplitterInterface
    {
        return new StringSplitter();
    }

    private function createTagFactory(): TagFactoryInterface
    {
        return new TagFactory();
    }

    private function createTagParser(TagFactoryInterface $factory): TagParserInterface
    {
        return new StringTagParser($factory);
    }

    private function createDescriptionParser(TagParserInterface $parser): DescriptionParserInterface
    {
        return new BalancedBraceAwareParser($parser);
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

    private function tryCreateDescription(string $description): ?DescriptionInterface
    {
        return $this->descriptions->tryParse($description);
    }

    public function parse(#[Language('InjectablePHP')] string $docblock): DocBlock
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
            if ($segment->isTag) {
                $blocks[] = $current;
                $current = '';
            }

            $current .= $text;
        }

        $blocks[] = $current;

        return new DocBlock(
            // The first section is always the description; the rest are tags.
            description: $this->tryCreateDescription(\array_shift($blocks)),
            tags: $this->createTags($blocks),
        );
    }
}
