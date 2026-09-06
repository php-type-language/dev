<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Type\TypeNode;

final class InMemoryTypeParser implements TypeParserInterface
{
    /**
     * @var non-empty-string
     */
    private const HASH_ALGORITHM = 'xxh128';

    /**
     * @var array<non-empty-string, TypeNode>
     */
    private array $types = [];

    /**
     * @var array<non-empty-string, ParsedResult>
     */
    private array $sequences = [];

    private readonly SourceFactoryInterface $sources;

    public function __construct(
        private readonly TypeParserInterface $parser = new TypeParser(),
        ?SourceFactoryInterface $sources = null,
    ) {
        $this->sources = $sources ?? SourceFactory::createDefault();
    }

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function parse(#[Language('PHP')] mixed $source): TypeNode
    {
        $instance = $this->sources->create($source);

        return $this->types[$this->hash($instance)] ??= $this->parser->parse($source);
    }

    /**
     * @throws ParserExceptionInterface
     * @throws SourceExceptionInterface
     * @throws \Throwable
     */
    public function parseTolerant(#[Language('PHP')] mixed $source): ParsedResult
    {
        $instance = $this->sources->create($source);

        return $this->sequences[$this->hash($instance)] ??= $this->parser->parseTolerant($source);
    }

    /**
     * @return non-empty-string
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function hash(ReadableInterface $source): string
    {
        return \hash(self::HASH_ALGORITHM, $source->content);
    }
}
