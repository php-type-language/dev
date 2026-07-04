<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\TypeParser;
use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Combinator\TypeCombinator;
use TypeLang\PhpDoc\DocBlock\TagDefinition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Reference\TypeReference;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\ExtendsTag;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\ExtendsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\InheritanceTag;
use TypeLang\PhpDoc\DocBlock\Tag\InvalidTag;
use TypeLang\PhpDoc\DocBlock\Tag\MixinTag\MixinTag;
use TypeLang\PhpDoc\DocBlock\Tag\MixinTag\MixinTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTag;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag\ThrowsTag;
use TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag\ThrowsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TypedTagInterface;
use TypeLang\PhpDoc\Exception\MalformedTagException;
use TypeLang\PhpDoc\TagFactory;
use TypeLang\PhpDoc\Tests\TestCase;
use TypeLang\Type\NamedTypeNode;

final class TypedTagTest extends TestCase
{
    #[Test]
    public function parsesTypeWithDescription(): void
    {
        $tag = self::factory()->create('return', 'int<0, max> The number of items.');

        self::assertInstanceOf(ReturnTag::class, $tag);
        self::assertInstanceOf(TypedTagInterface::class, $tag);
        self::assertSame('return', $tag->name);
        self::assertInstanceOf(NamedTypeNode::class, $tag->type);
        self::assertSame('int', (string) $tag->type->name);
        self::assertSame('The number of items.', (string) $tag->description);
        self::assertSame('@return int<0, max> The number of items.', (string) $tag);
    }

    #[Test]
    public function parsesTypeWithoutDescription(): void
    {
        $tag = self::factory()->create('throws', '\RuntimeException');

        self::assertInstanceOf(ThrowsTag::class, $tag);
        self::assertNull($tag->description);
        self::assertSame('@throws \RuntimeException', (string) $tag);
    }

    /**
     * The type text is preserved verbatim, including the inner whitespace of
     * generics and shapes.
     */
    #[Test]
    public function preservesComplexTypeSpelling(): void
    {
        $tag = self::factory()->create('mixin', 'array{id: int, name: string} rest');

        self::assertInstanceOf(MixinTag::class, $tag);
        self::assertSame('@mixin array{id: int, name: string} rest', (string) $tag);
    }

    #[Test]
    public function missingRequiredTypeProducesInvalidTag(): void
    {
        $tag = self::factory()->create('return', '');

        self::assertInstanceOf(InvalidTag::class, $tag);
        self::assertInstanceOf(MalformedTagException::class, $tag->reason);
    }

    /**
     * The name is intrinsic to the definition, never taken from the name it is
     * invoked with, so a definition always produces its own canonical name.
     */
    #[Test]
    public function nameIsIntrinsicToTheTag(): void
    {
        $statement = new TypeReference(new TypeParser()->parse('bool'), 'bool');

        $tag = new ReturnTagDefinition()
            ->create('whatever-was-written', new TagPayload(['type' => [$statement]]));

        self::assertInstanceOf(ReturnTag::class, $tag);
        self::assertSame('return', $tag->name);
    }

    /**
     * The inheritance tags share a meaning, so they are grouped under a common
     * base while remaining distinct types.
     */
    #[Test]
    public function inheritanceTagsShareACommonBase(): void
    {
        $tag = self::factory()->create('extends', 'Collection<int, string>');

        self::assertInstanceOf(ExtendsTag::class, $tag);
        self::assertInstanceOf(InheritanceTag::class, $tag);
        self::assertInstanceOf(TypedTagInterface::class, $tag);
        self::assertSame('extends', $tag->name);
        self::assertSame('@extends Collection<int, string>', (string) $tag);
    }

    /**
     * @return iterable<string, array{string, class-string<TypedTagInterface>, string}>
     */
    public static function tagProvider(): iterable
    {
        yield '@return' => ['return', ReturnTag::class, 'return'];
        yield '@throws' => ['throws', ThrowsTag::class, 'throws'];
        yield '@mixin' => ['mixin', MixinTag::class, 'mixin'];
        yield '@extends' => ['extends', ExtendsTag::class, 'extends'];

        // Documented aliases resolve to the same tag, but keep the canonical name.
        yield '@returns is an alias of @return' => ['returns', ReturnTag::class, 'return'];
        yield '@throw is an alias of @throws' => ['throw', ThrowsTag::class, 'throws'];
        yield '@inherits is an alias of @extends' => ['inherits', ExtendsTag::class, 'extends'];
        yield '@template-extends is an alias of @extends' => ['template-extends', ExtendsTag::class, 'extends'];
    }

    /**
     * @param class-string<TypedTagInterface> $expected
     * @param non-empty-string $canonical
     */
    #[Test]
    #[DataProvider('tagProvider')]
    public function tagResolvesThroughTheRealParser(string $name, string $expected, string $canonical): void
    {
        $block = new \TypeLang\PhpDoc\DocBlockParser()
            ->parse(\sprintf('/** @%s Some\\Type */', $name));

        self::assertCount(1, $block->tags);
        self::assertInstanceOf($expected, $block->tags[0]);
        self::assertSame($canonical, $block->tags[0]->name);
    }

    private static function factory(): TagFactory
    {
        return new TagFactory(
            definitions: [
                ReturnTagDefinition::NAME => new ReturnTagDefinition(),
                ThrowsTagDefinition::NAME => new ThrowsTagDefinition(),
                MixinTagDefinition::NAME => new MixinTagDefinition(),
                ExtendsTagDefinition::NAME => new ExtendsTagDefinition(),
            ],
            combinators: [
                TypeCombinator::NAME => new TypeCombinator(new TypeParser()),
                DescriptionCombinator::NAME => new DescriptionCombinator(self::createDescriptionParser()),
            ],
        );
    }
}
