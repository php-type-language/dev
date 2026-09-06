<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Contracts\Parser\Exception\RuntimeExceptionInterface as ParserRuntimeExceptionInterface;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Parser\Analysis\Result\FailureResult;
use Phplrt\Parser\Analysis\Result\PartialResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Parser\Exception\UnexpectedTokenException as GrammarUnexpectedTokenException;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Exception\InternalParseException;
use TypeLang\Parser\Exception\ParseException;
use TypeLang\Parser\Exception\SemanticException;
use TypeLang\Parser\Exception\SemanticParseException;
use TypeLang\Parser\Exception\UnexpectedTokenException;
use TypeLang\Parser\Exception\UnrecognizedSyntaxException;
use TypeLang\Parser\Exception\UnrecognizedTokenException;
use TypeLang\Parser\Internal\Executor;
use TypeLang\Type\TypeNode;

final class TypeParser implements TypeParserInterface
{
    private readonly SourceFactoryInterface $sources;

    private ?Executor $executor = null;

    public function __construct(
        public readonly TypeParserFeatures $features = new TypeParserFeatures(),
        ?SourceFactoryInterface $sources = null,
    ) {
        $this->sources = $sources ?? SourceFactory::createDefault();
    }

    /**
     * Returns a new parser with an overridden parser feature flag.
     *
     * ```
     * $parser = $parser->withFeatures(
     *     conditions: true,
     *     hints: false,
     * );
     * ```
     */
    public function withFeatures(bool ...$features): self
    {
        return new self(
            features: $this->features->with(...$features),
            sources: $this->sources,
        );
    }

    public function parse(#[Language('PHP')] mixed $source): TypeNode
    {
        $result = $this->execute($source, false);

        return $result->type;
    }

    public function parseTolerant(#[Language('PHP')] mixed $source): ParsedResult
    {
        return $this->execute($source, true);
    }

    /**
     * @throws ParseException in case of the source cannot be recognized
     */
    private function execute(mixed $source, bool $tolerant): ParsedResult
    {
        try {
            $instance = $this->sources->create($source);
        } catch (SourceExceptionInterface $e) {
            throw InternalParseException::becauseSourceIsUnreadable($e);
        }

        try {
            $result = $this->executor()->analyze($instance);

            // A source the grammar reads only in part is a syntax error unless
            // the tolerant mode has been asked for.
            if ($result instanceof FailureResult
                || (!$tolerant && $result instanceof PartialResult)
            ) {
                throw $this->grammarError($result->error, $instance);
            }

            return new ParsedResult(
                type: $this->fetchTypeStatement($result),
                offset: $this->fetchLastOffset($result, $instance),
            );
        } catch (ParseException $e) {
            throw $e;
        } catch (SemanticException $e) {
            throw $this->semanticError($e, $instance);
        } catch (SourceExceptionInterface $e) {
            throw InternalParseException::becauseSourceIsUnreadable($e);
        } catch (\Throwable $e) {
            throw $this->internalError($e, $instance);
        }
    }

    /**
     * @param SuccessfulResult<mixed> $result
     * @throws InternalParseException in case of the grammar has built
     *         something else than a type statement
     */
    private function fetchTypeStatement(SuccessfulResult $result): TypeNode
    {
        $statement = $result->value;

        if (!$statement instanceof TypeNode) {
            throw InternalParseException::becauseTypeStatementIsUnreadable();
        }

        return $statement;
    }

    /**
     * Returns the offset the analysis has stopped at.
     *
     * @param SuccessfulResult<mixed> $result
     * @return int<0, max>
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function fetchLastOffset(SuccessfulResult $result, ReadableInterface $source): int
    {
        // A source that has been read in full is left at its very end.
        if (!$result instanceof PartialResult) {
            /** @var int<0, max> */
            return \strlen($source->content);
        }

        return $result->token->offset;
    }

    /**
     * Converts the error of the grammar into the error of this parser.
     *
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function grammarError(ParserRuntimeExceptionInterface $e, ReadableInterface $source): ParseException
    {
        if (!$e instanceof GrammarUnexpectedTokenException) {
            return UnrecognizedSyntaxException::becauseSyntaxIsUnrecognized(
                statement: $source->content,
                offset: $e->token->offset,
            );
        }

        // An input the lexer says nothing about is reported as an unrecognized
        // one rather than as a token in a wrong place.
        if ($e->token->channel === Channel::Unknown) {
            return UnrecognizedTokenException::becauseTokenIsUnrecognized(
                token: $e->token->value,
                statement: $source->content,
                offset: $e->token->offset,
            );
        }

        return UnexpectedTokenException::becauseTokenIsUnexpected(
            token: $e->token->value,
            statement: $source->content,
            offset: $e->token->offset,
        );
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function semanticError(SemanticException $e, ReadableInterface $source): ParseException
    {
        return SemanticParseException::becauseSemanticErrorOccurs($e, $source);
    }

    /**
     * @throws SourceExceptionInterface in case of source content reading error
     */
    private function internalError(\Throwable $e, ReadableInterface $source): ParseException
    {
        return InternalParseException::becauseInternalErrorOccurs(
            statement: $source->content,
            e: $e,
        );
    }

    /**
     * Returns a lazily created parser recognizing a source with the features
     * of this one.
     */
    private function executor(): Executor
    {
        return $this->executor ??= new Executor($this->features);
    }
}
