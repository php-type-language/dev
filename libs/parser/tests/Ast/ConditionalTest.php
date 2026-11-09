<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Ast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\VariableNode;

/**
 * Tests for the conditional (ternary) types.
 */
#[Group('unit'), Group('type-lang/parser')]
final class ConditionalTest extends AstTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::markTestIncomplete('TODO: Conditions (step 17)');
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function conditionDataProvider(): iterable
    {
        yield 'is' => ['A is B ? C : D', 'EqualConditionNode'];
        yield 'is not' => ['A is not B ? C : D', 'NotEqualConditionNode'];
    }

    #[DataProvider('conditionDataProvider')]
    public function testConditionalOperators(string $type, string $condition): void
    {
        self::assertSame(<<<AST
            Condition\ConditionalTypeNode
              Condition\\{$condition}
                NamedTypeNode
                  Name(A)
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
              NamedTypeNode
                Name(D)
            AST, $this->parseAndPrint($type));
    }

    public function testConditionalWithVariableSubject(): void
    {
        self::assertSame(<<<'AST'
            Condition\ConditionalTypeNode
              Condition\EqualConditionNode
                VariableNode(name='T')
                NamedTypeNode
                  Name(B)
              NamedTypeNode
                Name(C)
              NamedTypeNode
                Name(D)
            AST, $this->parseAndPrint('$T is B ? C : D'));
    }

    /**
     * A variable stands on either side of the operator, and on both at once.
     *
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableOperandDataProvider(): iterable
    {
        yield 'on the left' => ['($T is B ? C : D)'];
        yield 'on the right' => ['(A is $T ? C : D)'];
        yield 'on both sides' => ['($A is $B ? C : D)'];
        yield 'this on the left' => ['($this is B ? C : D)'];
        yield 'this on the right' => ['(A is $this ? C : D)'];
    }

    /**
     * @param non-empty-string $type
     * @throws \Throwable
     */
    #[DataProvider('variableOperandDataProvider')]
    public function testVariableStandsAsAnOperand(string $type): void
    {
        $printer = new \TypeLang\Printer\PrettyTypePrinter();

        self::assertSame($type, $printer->print($this->parse($type)));
    }

    /**
     * A condition reads its subject as a type first, so the word is the type
     * and not the variable it is spelled like.
     */
    public function testThisStandsAsATypeOnTheLeftOfACondition(): void
    {
        $node = $this->parse('$this is B ? C : D');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(ThisTypeNode::class, $node->condition->subject);
    }

    /**
     * The token is written the one way it is written, so a word that only
     * looks like it is an ordinary variable.
     */
    public function testTheThisTokenIsCaseSensitive(): void
    {
        $node = $this->parse('$This is B ? C : D');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame('This', $node->condition->subject->name);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function variableNameDataProvider(): iterable
    {
        yield 'a word' => ['$value'];
        yield 'an underscore' => ['$value_1'];
        yield 'a leading underscore' => ['$_value'];
        yield 'digits' => ['$value42'];
        yield 'a word that opens with this' => ['$thisValue'];
    }

    /**
     * @param non-empty-string $variable
     * @throws \Throwable
     */
    #[DataProvider('variableNameDataProvider')]
    public function testVariableIsTheSubjectOfACondition(string $variable): void
    {
        $node = $this->parse($variable . ' is B ? C : D');

        self::assertInstanceOf(ConditionalTypeNode::class, $node);
        self::assertInstanceOf(VariableNode::class, $node->condition->subject);
        self::assertSame(\substr($variable, 1), $node->condition->subject->name);
    }
}
