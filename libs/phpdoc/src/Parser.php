<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc;

use TypeLang\PhpDoc\DocBlock\DocBlock;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\MutableTagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Exception\ParsingException;
use TypeLang\PhpDoc\Exception\RuntimeExceptionInterface;
use TypeLang\PhpDoc\Internal\Analyzer;
use TypeLang\PhpDoc\Internal\Splitter\SplitterInterface;
use TypeLang\PhpDoc\Internal\Splitter\StringSplitter;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Description\RegexDescriptionParser;
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
    private readonly Analyzer $sectioner;

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
        $this->sectioner = new Analyzer($this->createSplitter());
    }

    protected function createTagParser(TagFactoryInterface $factories): TagParserInterface
    {
        return new RegexTagParser($factories);
    }

    protected function createDescriptionParser(TagParserInterface $tags): DescriptionParserInterface
    {
        return new RegexDescriptionParser($tags);
    }

    protected function createSplitter(): SplitterInterface
    {
        return new StringSplitter();
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
        $sections = $this->sectioner->group($docblock);
        $map = $sections->map;

        $tags = [];

        // Offset (in the concatenated section space) of the section currently
        // being parsed; the source map translates it back to $docblock.
        $offset = 0;

        try {
            $description = $this->descriptions->parse($sections->description);
            $offset += \strlen($sections->description);

            foreach ($sections->tags as $tag) {
                $tags[] = $this->tags->parse($tag, $this->descriptions);
                $offset += \strlen($tag);
            }
        } catch (RuntimeExceptionInterface $e) {
            throw $e->withSource(
                source: $docblock,
                offset: $map->getOriginalOffset($offset + $e->getOffset()),
            );
        } catch (\Throwable $e) {
            throw ParsingException::fromInternalError(
                source: $docblock,
                offset: $map->getOriginalOffset($offset),
                e: $e,
            );
        }

        return new DocBlock($description, $tags);
    }
}
