<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\Group;

/**
 * Tests for the attribute grammar applied to template arguments and shape
 * fields (e.g. "#[name("key")]").
 *
 * Note: the AST dump renders the attribute structure (groups and names) but
 * not the attribute argument values.
 *
 * @see \TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode
 * @see \TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupNode
 * @see \TypeLang\Parser\Node\Stmt\Attribute\AttributeNode
 */
#[Group('unit'), Group('type-lang/parser')]
final class AttributeTest extends SyntaxTestCase
{
    public function testSingleAttributeOnTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Stmt\Attribute\AttributeGroupsListNode
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
            AST, $this->parseAndPrint('HashMap<#[name("key")] T>'));
    }

    public function testMultipleAttributesInOneGroup(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Stmt\Attribute\AttributeGroupsListNode
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
                      Stmt\Attribute\AttributeNode
                        Name(out)
                          Identifier(out)
            AST, $this->parseAndPrint('HashMap<#[name("key"), out] T>'));
    }

    public function testMultipleAttributeGroups(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(HashMap)
                Identifier(HashMap)
              Stmt\Template\TemplateArgumentsListNode
                Stmt\Template\TemplateArgumentNode
                  Stmt\NamedTypeNode
                    Name(T)
                      Identifier(T)
                  Stmt\Attribute\AttributeGroupsListNode
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(out)
                          Identifier(out)
            AST, $this->parseAndPrint('HashMap<#[name("key")] #[out] T>'));
    }

    public function testSingleAttributeOnShapeField(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(App\Domain\User)
                Identifier(App)
                Identifier(Domain)
                Identifier(User)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(required)
                  Stmt\NamedTypeNode
                    Name(non-empty-string)
                      Identifier(non-empty-string)
                  Stmt\Attribute\AttributeGroupsListNode
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(name)
                          Identifier(name)
                  Identifier(userName)
            AST, $this->parseAndPrint('App\\Domain\\User{#[name("user_name")] userName: non-empty-string}'));
    }

    public function testMultipleAttributeGroupsOnShapeField(): void
    {
        self::assertSame(<<<'AST'
            Stmt\NamedTypeNode
              Name(array)
                Identifier(array)
              Stmt\Shape\FieldsListNode(sealed)
                Stmt\Shape\NamedFieldNode(optional)
                  Stmt\NamedTypeNode
                    Name(App\Domain\User)
                      Identifier(App)
                      Identifier(Domain)
                      Identifier(User)
                  Stmt\Attribute\AttributeGroupsListNode
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(serialize)
                          Identifier(serialize)
                    Stmt\Attribute\AttributeGroupNode
                      Stmt\Attribute\AttributeNode
                        Name(deserialize)
                          Identifier(deserialize)
                  Identifier(test)
            AST, $this->parseAndPrint('array{#[serialize("onSerialize")] #[deserialize("onDeserialize")] test?: App\\Domain\\User}'));
    }

    public function testTemplateArgumentAttributeAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('unexpected "42"');

        $this->parse('Collection<#[42] User>');
    }

    public function testShapeFieldAttributeAllowsOnlyIdentifiers(): void
    {
        $this->expectParsingException('unexpected "42"');

        $this->parse('Collection{#[42] test?: User}');
    }
}
