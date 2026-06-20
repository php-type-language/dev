<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use JetBrains\PhpStorm\Language;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Parser\Parser;
use TypeLang\Parser\ParserInterface;
use TypeLang\Parser\Traverser;
use TypeLang\Type\Stmt\TypeStatement;

/**
 * @phpstan-type ParserOptionsType array{
 *     tolerant?: bool,
 *     conditional?: bool,
 *     shapes?: bool,
 *     callables?: bool,
 *     literals?: bool,
 *     generics?: bool,
 *     union?: bool,
 *     intersection?: bool,
 *     list?: bool,
 *     offsets?: bool,
 *     hints?: bool,
 *     attributes?: bool,
 * }
 */
#[Group('unit'), Group('type-lang/parser')]
abstract class TestCase extends BaseTestCase
{
    protected ParserInterface $parser {
        get => $this->parser ??= new Parser();
    }

    /**
     * @param ParserOptionsType $options
     */
    protected function parser(array $options = []): ParserInterface
    {
        if ($options === []) {
            return $this->parser;
        }

        return new Parser(...$options);
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function parse(#[Language('PHP')] string $code, array $options = []): TypeStatement
    {
        $parser = $this->parser($options);

        return $parser->parse($code);
    }

    protected function print(TypeStatement $statement): string
    {
        Traverser::new([$visitor = new Traverser\StringDumperVisitor()])
            ->traverse([$statement]);

        return \trim($visitor->getOutput());
    }

    /**
     * @param ParserOptionsType $options
     * @throws \Throwable
     */
    protected function parseAndPrint(#[Language('PHP')] string $code, array $options = []): string
    {
        $statement = $this->parse($code, $options);

        return $this->print($statement);
    }
}
