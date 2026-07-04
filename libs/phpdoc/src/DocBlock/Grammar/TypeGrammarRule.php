<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

use TypeLang\Parser\TypeParserInterface;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;
use TypeLang\Type\TypeNode;

/**
 * Reads a type from the cursor, consuming exactly the part that forms
 * a valid type and leaving any trailing text (such as a description) untouched.
 *
 * @implements RuleInterface<TypeNode>
 */
final readonly class TypeGrammarRule implements RuleInterface
{
    public const string NAME = 'Type';

    public function __construct(
        private TypeParserInterface $typeParser,
    ) {}

    public function __invoke(Cursor $cursor): TypeNode
    {
        $start = $cursor->position;
        $source = $cursor->readRemainder();

        if ($source === '') {
            throw new NoMatchException('Expected a type');
        }

        // Tolerant parsing yields the type together with the offset of the next
        // token after it, so the cursor is left at the start of the trailing
        // text (e.g. a description) rather than at the end of the buffer.
        $result = $this->typeParser->parseTolerant($source);

        $cursor->position = $start + $result->offset;

        return $result->type;
    }
}
