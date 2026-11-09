<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use Phplrt\Source\StringSource;
use TypeLang\Parser\Internal\Executor;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\VariableNode;

/**
 * The variable `$name` is a node of its own: the name behind the dollar sign
 * and the offset of the dollar sign.
 */
final class VariableNodeTest extends TestCase
{
    private function readVariable(string $source): mixed
    {
        return (new Executor(new TypeParserFeatures()))
            ->for(Executor::Variable)
            ->parse(new StringSource($source));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function nameDataProvider(): iterable
    {
        yield 'letter' => ['$a', 'a'];
        yield 'word' => ['$value', 'value'];
        yield 'underscore in front' => ['$_x', '_x'];
        yield 'upper case' => ['$ABC', 'ABC'];
        yield 'camel case' => ['$someValue', 'someValue'];
        yield 'unicode' => ['$Ф', 'Ф'];
        yield 'this and a suffix' => ['$thisFoo', 'thisFoo'];
        yield 'this in upper case' => ['$THIS', 'THIS'];
        yield 'with trivia around' => ["/* c */ \$a // c\n", 'a'];
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $name
     */
    #[DataProvider('nameDataProvider')]
    public function testNameIsTheTextBehindTheDollarSign(string $source, string $name): void
    {
        $node = $this->readVariable($source);

        self::assertInstanceOf(VariableNode::class, $node);
        self::assertSame($name, $node->name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, int<0, max>}>
     */
    public static function offsetDataProvider(): iterable
    {
        yield 'no trivia' => ['$a', 0];
        yield 'spaces before' => ['  $a', 2];
        yield 'line breaks before' => ["\n\n\$a", 2];
        yield 'comment before' => ['/* c */$a', 7];
        yield 'multibyte comment before' => ['/* Фу */ $a', 11];
    }

    /**
     * The node begins at the dollar sign.
     *
     * @param non-empty-string $source
     * @param int<0, max> $offset
     */
    #[DataProvider('offsetDataProvider')]
    public function testOffsetIsTheOffsetOfTheDollarSign(string $source, int $offset): void
    {
        $node = $this->readVariable($source);

        self::assertInstanceOf(VariableNode::class, $node);
        self::assertSame($offset, $node->offset);
    }

    public function testThisIsATypeNotAVariable(): void
    {
        self::assertInstanceOf(ThisTypeNode::class, $this->parse('$this'));
    }

    public function testThisIsNotReadAsAVariable(): void
    {
        $this->expectException(\Throwable::class);

        $this->readVariable('$this');
    }

    public function testVariableIsNoType(): void
    {
        $this->expectException(\Throwable::class);

        $this->parse('$a');
    }
}
