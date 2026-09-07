<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Parser\Exception\ParserExceptionInterface;
use Phplrt\Contracts\Parser\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Parser\Analysis\Mode;
use Phplrt\Parser\Analysis\Result\FailureResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Parser\Parser as ParserRuntime;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\TypeNode;

/**
 * The compiled TypeLang grammar, bound to the set of features the source is
 * recognized with.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 *
 * @template-extends CompilerExecutor<TypeNode>
 */
final class Executor extends CompilerExecutor
{
    public function __construct(
        /**
         * @api this property is accessible inside the grammar reducers
         */
        protected readonly TypeParserFeatures $features,
    ) {
        parent::__construct();
    }

    /**
     * Reads as much of the given source as the grammar describes and reports
     * what it has been made of.
     *
     * @throws ParserExceptionInterface in case of the parser cannot be built
     * @throws RuntimeExceptionInterface in case of the source cannot be read
     * @throws \Throwable in case of an internal error occurs
     */
    public function analyze(
        ReadableInterface $source,
        Mode $mode = Mode::Tolerant,
    ): SuccessfulResult|FailureResult {
        $parser = $this->parser;

        if (!$parser instanceof ParserRuntime) {
            throw new \LogicException('The compiled grammar must be built on top of the phplrt parser');
        }

        return $parser->analyze($source, $mode);
    }
}
