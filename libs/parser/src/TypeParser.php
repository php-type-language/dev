<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use JetBrains\PhpStorm\Language;
use Phplrt\Contracts\Source\Exception\SourceExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Contracts\Source\SourceFactoryInterface;
use Phplrt\Source\SourceFactory;
use TypeLang\Parser\Internal\Executor;
use TypeLang\Parser\Partial\ParsedResult;
use TypeLang\Parser\Validation\CheckResult;
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
        $executor = $this->getExecutor();

        return $executor->parse($this->source($source));
    }

    public function partial(#[Language('PHP')] mixed $source): ParsedResult
    {
        $executor = $this->getExecutor();

        return $executor->partial($this->source($source));
    }

    public function validate(#[Language('PHP')] mixed $source): CheckResult
    {
        $executor = $this->getExecutor();

        return $executor->validate($this->source($source));
    }

    /**
     * @throws SourceExceptionInterface in case of no source can be created out
     *         of the given value
     */
    private function source(mixed $source): ReadableInterface
    {
        return $this->sources->create($source);
    }

    /**
     * Returns a lazily created parser recognizing a source with the features
     * of this one.
     */
    private function getExecutor(): Executor
    {
        return $this->executor ??= new Executor($this->features);
    }
}
