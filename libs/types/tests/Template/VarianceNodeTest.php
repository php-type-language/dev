<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Node;
use TypeLang\Type\Template\VarianceNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

/**
 * Tests for the variance standing in front of a template argument or a parameter.
 */
final class VarianceNodeTest extends TestCase
{
    #[Test]
    public function varianceIsANodeAndNoType(): void
    {
        $node = new VarianceNode(VarianceKind::Covariant);

        self::assertInstanceOf(Node::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function constructorKeepsTheKindAndTheOffset(): void
    {
        $node = new VarianceNode(VarianceKind::Contravariant, 7);

        self::assertSame(VarianceKind::Contravariant, $node->kind);
        self::assertSame(7, $node->offset);
    }

    #[Test]
    public function offsetIsZeroByDefault(): void
    {
        self::assertSame(0, (new VarianceNode(VarianceKind::Invariant))->offset);
    }

    #[Test]
    public function kindCannotBeReplaced(): void
    {
        $node = new VarianceNode(VarianceKind::Covariant);

        $this->expectException(\Error::class);

        /** @phpstan-ignore-next-line the property is readonly, this is the point of the test */
        $node->kind = VarianceKind::Invariant;
    }

    /**
     * @return iterable<non-empty-string, array{VarianceKind, non-empty-string}>
     */
    public static function kindDataProvider(): iterable
    {
        yield 'covariant' => [VarianceKind::Covariant, 'covariant'];
        yield 'contravariant' => [VarianceKind::Contravariant, 'contravariant'];
        yield 'invariant' => [VarianceKind::Invariant, 'invariant'];
    }

    /**
     * @param non-empty-string $word
     */
    #[Test]
    #[DataProvider('kindDataProvider')]
    public function kindIsTheWordTheVarianceIsWrittenWith(VarianceKind $kind, string $word): void
    {
        self::assertSame($word, $kind->value);
        self::assertSame($kind, VarianceKind::from($word));
    }

    #[Test]
    public function thereAreThreeKinds(): void
    {
        self::assertCount(3, VarianceKind::cases());
    }

    #[Test]
    #[DataProvider('unknownWordDataProvider')]
    public function unknownWordIsNoKind(string $word): void
    {
        self::assertNull(VarianceKind::tryFrom($word));
    }

    /**
     * @return iterable<non-empty-string, array{string}>
     */
    public static function unknownWordDataProvider(): iterable
    {
        yield 'in' => ['in'];
        yield 'out' => ['out'];
        yield 'upper case' => ['COVARIANT'];
        yield 'empty' => [''];
    }
}
