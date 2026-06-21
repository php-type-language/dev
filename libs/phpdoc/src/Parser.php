<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc;

use TypeLang\PhpDoc\DocBlock\DocBlock;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\MutableTagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Exception\ParsingException;
use TypeLang\PhpDoc\Exception\RuntimeExceptionInterface;
use TypeLang\PhpDoc\Parser\Comment\CommentParserInterface;
use TypeLang\PhpDoc\Parser\Comment\RegexCommentParser;
use TypeLang\PhpDoc\Parser\Comment\Segment;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Description\RegexDescriptionParser;
use TypeLang\PhpDoc\Parser\SourceMap;
use TypeLang\PhpDoc\Parser\Tag\RegexTagParser;
use TypeLang\PhpDoc\Parser\Tag\TagParserInterface;
use TypeLang\PhpDoc\Platform\CompoundPlatform;
use TypeLang\PhpDoc\Platform\PhanPlatform;
use TypeLang\PhpDoc\Platform\PHPStanPlatform;
use TypeLang\PhpDoc\Platform\PlatformInterface;
use TypeLang\PhpDoc\Platform\PsalmPlatform;
use TypeLang\PhpDoc\Platform\StandardPlatform;

class Parser implements ParserInterface
{
    private readonly CommentParserInterface $comments;

    private readonly DescriptionParserInterface $descriptions;

    private readonly TagParserInterface $tags;

    private readonly MutableTagFactoryInterface $factories;

    public function __construct(
        public readonly PlatformInterface $platform = new CompoundPlatform([
            new StandardPlatform(),
            new PsalmPlatform(),
            new PHPStanPlatform(),
            new PhanPlatform(),
        ]),
    ) {
        $this->factories = new TagFactory($platform->getTags());
        $this->tags = $this->createTagParser($this->factories);
        $this->descriptions = $this->createDescriptionParser($this->tags);
        $this->comments = $this->createCommentParser();
    }

    protected function createTagParser(TagFactoryInterface $factories): TagParserInterface
    {
        return new RegexTagParser($factories);
    }

    protected function createDescriptionParser(TagParserInterface $tags): DescriptionParserInterface
    {
        return new RegexDescriptionParser($tags);
    }

    protected function createCommentParser(): CommentParserInterface
    {
        return new RegexCommentParser();
    }

    /**
     * Facade method of {@see MutableTagFactoryInterface::register()}
     *
     * @param non-empty-lowercase-string|list<non-empty-lowercase-string> $tags
     */
    public function register(string|array $tags, TagFactoryInterface $delegate): void
    {
        $this->factories->register($tags, $delegate);
    }

    /**
     * @throws RuntimeExceptionInterface
     */
    public function parse(string $docblock): DocBlock
    {
        $mapper = new SourceMap();

        try {
            /** @var Segment $segment */
            foreach ($result = $this->analyze($docblock) as $segment) {
                $mapper->add($segment->offset, $segment->text);
            }
        } catch (RuntimeExceptionInterface $e) {
            throw $e->withSource(
                source: $docblock,
                offset: $mapper->getOffset($e->getOffset()),
            );
        } catch (\Throwable $e) {
            throw ParsingException::fromInternalError(
                source: $docblock,
                offset: $mapper->getOffset(0),
                e: $e,
            );
        }

        return $result->getReturn();
    }

    /**
     * @return \Generator<array-key, Segment, void, DocBlock>
     * @throws RuntimeExceptionInterface
     */
    private function analyze(string $docblock): \Generator
    {
        yield from $blocks = $this->groupByCommentSections($docblock);

        $description = null;
        $tags = [];
        $offset = 0;

        foreach ($blocks->getReturn() as $block) {
            try {
                if ($description === null) {
                    $description = $this->descriptions->parse($block);
                } else {
                    $tags[] = $this->tags->parse($block, $this->descriptions);
                }
            } catch (RuntimeExceptionInterface $e) {
                throw $e->withSource(
                    source: $docblock,
                    offset: $offset + $e->getOffset(),
                );
            } catch (\Throwable $e) {
                throw ParsingException::fromInternalError(
                    source: $docblock,
                    offset: $offset,
                    e: $e,
                );
            }

            $offset += \strlen($block);
        }

        return new DocBlock($description, $tags);
    }

    /**
     * @return \Generator<array-key, Segment, void, non-empty-list<string>>
     */
    private function groupByCommentSections(string $docblock): \Generator
    {
        $current = '';
        $blocks = [];

        foreach ($this->comments->parse($docblock) as $segment) {
            yield $segment;

            if (\str_starts_with($segment->text, '@')) {
                $blocks[] = $current;
                $current = '';
            }

            $current .= $segment->text;
        }

        return [...$blocks, $current];
    }
}
