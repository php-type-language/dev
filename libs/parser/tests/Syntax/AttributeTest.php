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
 */
#[Group('unit'), Group('type-lang/parser')]
final class AttributeTest extends SyntaxTestCase
{
    public function testSingleAttributeOnTemplateArgument(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
            AST, $this->parseAndPrint('HashMap<#[name("key")] T>'));
    }

    public function testMultipleAttributesInOneGroup(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
                      Attribute\AttributeNode
                        Name(out)
            AST, $this->parseAndPrint('HashMap<#[name("key"), out] T>'));
    }

    public function testMultipleAttributeGroups(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(HashMap)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(T)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(out)
            AST, $this->parseAndPrint('HashMap<#[name("key")] #[out] T>'));
    }

    public function testSingleAttributeOnShapeField(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(App\Domain\User)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=false)
                  Identifier(userName)
                  NamedTypeNode
                    Name(non-empty-string)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(name)
            AST, $this->parseAndPrint('App\\Domain\\User{#[name("user_name")] userName: non-empty-string}'));
    }

    public function testMultipleAttributeGroupsOnShapeField(): void
    {
        self::assertSame(<<<'AST'
            NamedTypeNode
              Name(array)
              Shape\FieldsListNode(isSealed=true)
                Shape\NamedFieldNode(isOptional=true)
                  Identifier(test)
                  NamedTypeNode
                    Name(App\Domain\User)
                  Attribute\AttributeGroupListNode
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(serialize)
                    Attribute\AttributeGroupNode
                      Attribute\AttributeNode
                        Name(deserialize)
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
