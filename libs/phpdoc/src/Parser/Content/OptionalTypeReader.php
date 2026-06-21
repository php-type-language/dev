<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Parser\Content;

use TypeLang\Node\FullQualifiedName;
use TypeLang\Node\Type\TypeNode;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Exception\InvalidTagException;

/**
 * @template-implements OptionalReaderInterface<TypeNode>
 */
final class OptionalTypeReader implements OptionalReaderInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser,
    ) {}

    /**
     * @throws \Throwable
     * @throws InvalidTagException
     */
    public function __invoke(Stream $stream): ?TypeNode
    {
        try {
            $type = $this->parser->parse($stream->value);
        } catch (ParserExceptionInterface) {
            return null;
        }

        // @phpstan-ignore-next-line : Property is defined
        $stream->shift($this->parser->lastProcessedTokenOffset);

        return $type;
    }
}
