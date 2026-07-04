<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc;

use JetBrains\PhpStorm\Language;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\DocBlock;
use TypeLang\PhpDoc\DocBlock\Grammar\UriGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\Link\LinkTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinitionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\TagInterface;
use TypeLang\PhpDoc\Exception\ParsingException;
use TypeLang\PhpDoc\Exception\PhpDocExceptionInterface;
use TypeLang\PhpDoc\Exception\TagParsingException;
use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\DocBlockAnalyzer;
use TypeLang\PhpDoc\Parser\Grammar\Grammar;
use TypeLang\PhpDoc\Parser\Splitter\Segment;
use TypeLang\PhpDoc\Parser\Splitter\SplitterInterface;
use TypeLang\PhpDoc\Parser\Splitter\StringSplitter;
use TypeLang\PhpDoc\Parser\Tag\StringTagParser;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;

final readonly class DocBlockParser implements DocBlockParserInterface
{
    public TagFactoryInterface $tags;

    private DocBlockAnalyzer $docBlockAnalyzer;
    private TagParserInterface $tagParser;
    private DescriptionParserInterface $descriptionParser;

    public function __construct()
    {
        $this->docBlockAnalyzer = $this->createAnalyzer(
            splitter: $this->createDocBlockSplitter(),
        );

        $this->tagParser = $this->createTagParser(
            factory: $this->tags = $this->createTagFactory(),
        );

        $this->descriptionParser = $this->createDescriptionParser(
            parser: $this->tagParser,
        );
    }

    private function createAnalyzer(SplitterInterface $splitter): DocBlockAnalyzer
    {
        return new DocBlockAnalyzer($splitter);
    }

    private function createDocBlockSplitter(): SplitterInterface
    {
        return new StringSplitter();
    }

    private function createDefaultGrammar(): Grammar
    {
        return new Grammar([
            UriGrammarRule::NAME => new UriGrammarRule(),
        ]);
    }

    /**
     * @return array<non-empty-string, TagDefinitionInterface>
     */
    private function createDefaultTagDefinitions(): array
    {
        return [
            LinkTagDefinition::NAME => new LinkTagDefinition(),
        ];
    }

    private function createTagFactory(): TagFactoryInterface
    {
        return new TagFactory(
            definitions: $this->createDefaultTagDefinitions(),
            grammar: $this->createDefaultGrammar(),
        );
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
     * @param list<Segment> $segments
     * @return list<TagInterface>
     * @throws PhpDocExceptionInterface
     */
    private function createTags(array $segments, string $docblock): array
    {
        $result = [];

        foreach ($segments as $segment) {
            try {
                $result[] = $this->tagParser->parse($segment->text, $this->descriptionParser);
            } catch (\Throwable $e) {
                throw $this->failure($e, $docblock, $segment->offset);
            }
        }

        return $result;
    }

    /**
     * @throws PhpDocExceptionInterface
     */
    private function tryCreateDescription(Segment $segment, string $docblock): ?DescriptionInterface
    {
        try {
            return $this->descriptionParser->tryParse($segment->text);
        } catch (\Throwable $e) {
            throw $this->failure($e, $docblock, $segment->offset);
        }
    }

    /**
     * Guarantees that only a {@see PhpDocExceptionInterface} leaves the parser.
     *
     * A parsing error is rebased onto the full docblock so that its offset
     * becomes absolute (e.g. the position of the tag that failed to parse). Any
     * other, internal failure is wrapped as a {@see TagParsingException} at the
     * same location.
     *
     * @param int<0, max> $offset byte offset of the failing section inside $source
     */
    private function failure(\Throwable $e, string $source, int $offset): PhpDocExceptionInterface
    {
        // A parsing error carries an offset relative to the failing section;
        // rebasing it onto $source turns that into an absolute location.
        if ($e instanceof ParsingException) {
            return $e->withSource($source, $e->offset + $offset);
        }

        // Any other library error is already contractual and passes through.
        if ($e instanceof PhpDocExceptionInterface) {
            return $e;
        }

        // An internal (non-library) failure is wrapped so that the whole
        // docblock and the failing offset are still reported to the caller.
        return TagParsingException::becauseInternalErrorOccurs($e, $source, $offset);
    }

    public function parse(#[Language('InjectablePHP')] string $docblock): DocBlock
    {
        $data = $this->docBlockAnalyzer->analyze($docblock);

        return new DocBlock(
            description: $this->tryCreateDescription($data->description, $docblock),
            tags: $this->createTags($data->tags, $docblock),
        );
    }
}
