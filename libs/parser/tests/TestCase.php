<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Parser\Parser;
use TypeLang\Parser\ParserInterface;

#[Group('unit'), ('type-lang/parser')]
abstract class TestCase extends BaseTestCase
{
    protected ParserInterface $parser {
        get => $this->parser ??= new Parser();
    }

    /**
     * @param array{
     *     tolerant: bool,
     *     conditional: bool,
     *     shapes: bool,
     *     callables: bool,
     *     literals: bool,
     *     generics: bool,
     *     union: bool,
     *     intersection: bool,
     *     list: bool,
     *     offsets: bool,
     *     hints: bool,
     *     attributes: bool,
     * } $options
     */
    protected function parser(array $options = []): ParserInterface
    {
        return new Parser(...$options);
    }
}
