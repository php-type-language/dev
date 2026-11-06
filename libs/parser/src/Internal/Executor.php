<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Lexer\Exception\LexerExceptionInterface;
use Phplrt\Contracts\Lexer\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Position\PositionInterface;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Parser\Analysis\Mode;
use Phplrt\Parser\Analysis\Result\FailureResult;
use Phplrt\Parser\Analysis\Result\PartialResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Parser\Exception\UnexpectedTokenException as GrammarUnexpectedTokenException;
use Phplrt\Position\PositionFactory;
use TypeLang\Parser\Exception\InternalParseException;
use TypeLang\Parser\Exception\ParserException;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Exception\UnreadableSourceException;
use TypeLang\Parser\Exception\UnrecognizedSyntaxException;
use TypeLang\Parser\Exception\UnrecognizedTokenException;
use TypeLang\Parser\Partial\FailureParsedResult;
use TypeLang\Parser\Partial\PartialParsedResult;
use TypeLang\Parser\Partial\SuccessfulParsedResult;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Parser\Validation\FailureCheckResult;
use TypeLang\Parser\Validation\PartialCheckResult;
use TypeLang\Parser\Validation\SuccessfulCheckResult;
use TypeLang\Type\Node;

/**
 * The compiled TypeLang grammar, bound to the set of features the source is
 * recognized with.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 *
 * @template T of Node = \TypeLang\Type\TypeNode
 *
 * @template-extends CompiledExecutor<T>
 */
final class Executor extends CompiledExecutor
{
    private readonly PositionFactory $positions;

    public function __construct(
        /**
         * @api this property is accessible inside the grammar reducers
         */
        protected readonly TypeParserFeatures $features,
    ) {
        $this->positions = new PositionFactory();

        parent::__construct();
    }

    /**
     * @param int<0, max> $rule
     * @return self<Node>
     */
    public function for(int $rule): self
    {
        $self = clone $this;
        $self->parser = $self->parser->withInitial($rule);

        return $self;
    }

    /**
     * @return iterable<array-key, TokenInterface>
     * @throws LexerExceptionInterface
     * @throws RuntimeExceptionInterface
     */
    public function lex(ReadableInterface $source): iterable
    {
        return $this->lexer->lex($source);
    }

    /**
     * Reads the source whole and returns the type it is written of.
     *
     * @throws ParserException in case of the source is no type of its own
     */
    public function parse(ReadableInterface $source): Node
    {
        $result = $this->build($source);

        if (!$result instanceof SuccessfulResult || $result instanceof PartialResult) {
            throw $this->createError($result, $source);
        }

        /** @var T */
        return $result->value;
    }

    /**
     * Reads as much of the source as the grammar describes and returns the
     * type that part is written of.
     *
     * @return SuccessfulParsedResult<T>|FailureParsedResult
     * @throws ParserException in case of an internal error occurs
     * @throws SourceExceptionInterface
     * @throws ParserExceptionInterface
     */
    public function partial(ReadableInterface $source): SuccessfulParsedResult|FailureParsedResult
    {
        /** @var FailureResult|PartialResult<T>|SuccessfulResult<T> $result */
        $result = $this->build($source);

        if ($result instanceof FailureResult) {
            return new FailureParsedResult(
                message: $this->createError($result, $source)
                    ->getMessage(),
                position: $this->createPosition($source, $result->token->offset),
                offset: $result->token->offset,
            );
        }

        /** @var T $value */
        $value = $result->value;

        if ($result instanceof PartialResult) {
            return new PartialParsedResult(
                type: $value,
                offset: $result->token->offset,
            );
        }

        return new SuccessfulParsedResult(
            type: $value,
            offset: $this->length($source),
        );
    }

    /**
     * Tells whether the source is a type the grammar describes, building
     * nothing of it.
     *
     * @throws ParserException in case of an internal error occurs
     * @throws SourceExceptionInterface
     */
    public function validate(ReadableInterface $source): SuccessfulCheckResult|FailureCheckResult
    {
        $result = $this->check($source);

        if ($result instanceof SuccessfulResult && !$result instanceof PartialResult) {
            return new SuccessfulCheckResult();
        }

        $error = $this->createError($result, $source);
        $offset = $result->token->offset;
        $position = $this->createPosition($source, $offset);

        if ($result instanceof PartialResult) {
            return new PartialCheckResult(
                message: $error->getMessage(),
                position: $position,
                offset: $offset,
            );
        }

        return new FailureCheckResult(
            message: $error->getMessage(),
            position: $position,
            offset: $offset,
        );
    }

    /**
     * Reads the source into the type it describes.
     *
     * @return SuccessfulResult<T>|FailureResult
     * @throws ParserException in case of the grammar cannot be run
     */
    private function build(ReadableInterface $source): SuccessfulResult|FailureResult
    {
        try {
            return $this->parser->analyze($source, Mode::Tolerant);
        } catch (\Throwable $e) {
            throw $this->raised($e, $source);
        }
    }

    /**
     * Reads the source without building anything of it.
     *
     * @return SuccessfulResult<null>|FailureResult
     * @throws ParserException in case of the grammar cannot be run
     */
    private function check(ReadableInterface $source): SuccessfulResult|FailureResult
    {
        try {
            return $this->parser->analyze($source, Mode::SyntaxCheck);
        } catch (\Throwable $e) {
            throw $this->raised($e, $source);
        }
    }

    /**
     * Converts whatever the grammar raises while it reads into the error of
     * this parser.
     */
    private function raised(\Throwable $e, ReadableInterface $source): ParserException
    {
        return match (true) {
            $e instanceof ParserException => $e,
            $e instanceof SourceExceptionInterface
                => UnreadableSourceException::becauseSourceIsUnreadable($source, $e),
            default => InternalParseException::becauseInternalErrorOccurs($source, $e),
        };
    }

    /**
     * Returns the length of the source, which is the offset a reading that
     * has stopped at nothing ends at.
     *
     * @return int<0, max>
     * @throws ParserExceptionInterface in case of the source cannot be read
     */
    private function length(ReadableInterface $source): int
    {
        try {
            return \strlen($source->content);
        } catch (\Throwable $e) {
            throw $this->raised($e, $source);
        }
    }

    /**
     * @param int<0, max> $offset
     * @throws SourceExceptionInterface
     */
    private function createPosition(ReadableInterface $source, int $offset): PositionInterface
    {
        return $this->positions->createFromOffset($source, $offset);
    }

    /**
     * Converts the error of the grammar into the error of this parser.
     *
     * @param FailureResult|PartialResult<mixed> $result
     */
    private function createError(FailureResult|PartialResult $result, ReadableInterface $source): ParserException
    {
        $error = $result->error;

        if (!$error instanceof GrammarUnexpectedTokenException) {
            return UnrecognizedSyntaxException::becauseSyntaxIsUnrecognized($source, $error->token);
        }

        // An input the lexer says nothing about is an unrecognized token.
        if ($error->token->channel === Channel::Unknown) {
            return UnrecognizedTokenException::becauseTokenIsUnrecognized($source, $error->token);
        }

        return UnexpectedTokenException::becauseTokenIsUnexpected(
            message: $error->getMessage(),
            source: $source,
            token: $error->token,
        );
    }
}
