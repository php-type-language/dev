<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Syntax;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Exception\FeatureNotEnabledException;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * The features `lists`, `offsets` and `nullables` turn the suffixes `[]`, `[K]`
 * and the question mark on and off.
 */
final class SuffixFeatureTest extends TestCase
{
    /**
     * Every feature is on by default.
     */
    public function testEverythingIsAllowedByDefault(): void
    {
        $parser = new TypeParser();

        self::assertInstanceOf(TypesListTypeNode::class, $parser->parse('int[]'));
        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $parser->parse('T[K]'));
        self::assertInstanceOf(NullableTypeNode::class, $parser->parse('?int'));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, non-empty-string, int<0, max>}>
     */
    public static function disabledDataProvider(): iterable
    {
        // The feature, the source, the message, the offset of the first character that is not allowed
        yield 'list' => ['lists', 'int[]', 'Lists feature is not allowed', 3];
        yield 'list with spaces' => ['lists', 'int  [ ]', 'Lists feature is not allowed', 5];
        yield 'second list' => ['lists', 'int[][]', 'Lists feature is not allowed', 3];
        yield 'list of a group' => ['lists', '(int)[]', 'Lists feature is not allowed', 5];
        yield 'list behind an access' => ['lists', 'T[K][]', 'Lists feature is not allowed', 4];
        yield 'list in a key' => ['lists', 'T[K[]]', 'Lists feature is not allowed', 3];
        yield 'list in a nullable' => ['lists', '?int[]', 'Lists feature is not allowed', 4];
        yield 'access' => ['offsets', 'T[K]', 'Offsets feature is not allowed', 1];
        yield 'access with spaces' => ['offsets', 'T  [ K ]', 'Offsets feature is not allowed', 3];
        yield 'second access' => ['offsets', 'T[K][L]', 'Offsets feature is not allowed', 1];
        yield 'access of a group' => ['offsets', '(T)[K]', 'Offsets feature is not allowed', 3];
        yield 'access behind a list' => ['offsets', 'T[][K]', 'Offsets feature is not allowed', 3];
        yield 'access in a key' => ['offsets', 'T[K[L]]', 'Offsets feature is not allowed', 3];
        yield 'access in a nullable' => ['offsets', '?T[K]', 'Offsets feature is not allowed', 2];
        yield 'nullable' => ['nullables', '?int', 'Nullables feature is not allowed', 0];
        yield 'nullable with a space' => ['nullables', '  ? int', 'Nullables feature is not allowed', 2];
        yield 'nullable of a list' => ['nullables', '?int[]', 'Nullables feature is not allowed', 0];
        yield 'nullable of a group' => ['nullables', '?(int)', 'Nullables feature is not allowed', 0];
        yield 'nullable in a group' => ['nullables', '(?int)', 'Nullables feature is not allowed', 1];
        yield 'nullable in a key' => ['nullables', 'T[?K]', 'Nullables feature is not allowed', 2];
        // The inner one is built first, so it is the one that is reported
        yield 'nullable in a nullable' => ['nullables', '?(?int)', 'Nullables feature is not allowed', 2];
    }

    /**
     * @param non-empty-string $feature
     * @param non-empty-string $source
     * @param non-empty-string $message
     * @param int<0, max> $offset
     */
    #[DataProvider('disabledDataProvider')]
    public function testDisabledFeatureIsRejected(string $feature, string $source, string $message, int $offset): void
    {
        $parser = new TypeParser(new TypeParserFeatures(...[$feature => false]));

        try {
            $parser->parse($source);
        } catch (FeatureNotEnabledException $e) {
            self::assertSame($message, $e->getMessage());
            self::assertSame($offset, $e->range->offset);
            self::assertSame(1, $e->range->length);

            return;
        }

        self::fail(\sprintf('The source %s must be rejected', \json_encode($source, \JSON_THROW_ON_ERROR)));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string, class-string<TypeNode>}>
     */
    public static function otherFeaturesDataProvider(): iterable
    {
        // The feature that is off, the source that does not use it, the node it makes
        yield 'lists off, access' => ['lists', 'T[K]', TypeOffsetAccessTypeNode::class];
        yield 'lists off, nullable' => ['lists', '?T', NullableTypeNode::class];
        yield 'lists off, nullable access' => ['lists', '?T[K]', NullableTypeNode::class];
        yield 'offsets off, list' => ['offsets', 'T[]', TypesListTypeNode::class];
        yield 'offsets off, nullable' => ['offsets', '?T', NullableTypeNode::class];
        yield 'offsets off, nullable list' => ['offsets', '?T[]', NullableTypeNode::class];
        yield 'nullables off, list' => ['nullables', 'T[]', TypesListTypeNode::class];
        yield 'nullables off, access' => ['nullables', 'T[K]', TypeOffsetAccessTypeNode::class];
        yield 'nullables off, list of groups' => ['nullables', '(T)[][K]', TypeOffsetAccessTypeNode::class];
    }

    /**
     * A feature that is off does not touch the other ones.
     *
     * @param non-empty-string $feature
     * @param non-empty-string $source
     * @param class-string<TypeNode> $class
     */
    #[DataProvider('otherFeaturesDataProvider')]
    public function testDisabledFeatureDoesNotTouchTheOthers(string $feature, string $source, string $class): void
    {
        $parser = new TypeParser(new TypeParserFeatures(...[$feature => false]));

        self::assertInstanceOf($class, $parser->parse($source));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function plainTypesDataProvider(): iterable
    {
        yield 'name' => ['int'];
        yield 'qualified name' => ['\\Foo\\Bar'];
        yield 'group' => ['(int)'];
        yield 'groups' => ['(((int)))'];
        yield 'this' => ['$this'];
        yield 'class constant' => ['Foo::BAR'];
        yield 'global mask' => ['PHP_*'];
        yield 'literal' => ['42'];
    }

    /**
     * Types with no suffixes and no question marks are never rejected.
     *
     * @param non-empty-string $source
     */
    #[DataProvider('plainTypesDataProvider')]
    public function testPlainTypesNeedNoFeatureOfThem(string $source): void
    {
        $parser = new TypeParser(new TypeParserFeatures(lists: false, offsets: false, nullables: false));

        self::assertInstanceOf(TypeNode::class, $parser->parse($source));
    }

    public function testAllThreeFeaturesCanBeTurnedOffAtOnce(): void
    {
        $parser = new TypeParser(new TypeParserFeatures(lists: false, offsets: false, nullables: false));

        foreach (['int[]', 'T[K]', '?int', '?T[?K]'] as $source) {
            try {
                $parser->parse($source);
            } catch (FeatureNotEnabledException) {
                continue;
            }

            self::fail(\sprintf('The source %s must be rejected', $source));
        }

        $this->addToAssertionCount(1);
    }

    public function testFeaturesAreChangedWithTheParser(): void
    {
        $parser = (new TypeParser())->withFeatures(lists: false);

        self::assertInstanceOf(TypeOffsetAccessTypeNode::class, $parser->parse('T[K]'));

        $this->expectException(FeatureNotEnabledException::class);

        $parser->parse('T[]');
    }

    public function testDefaultParserIsNotAffectedByAnotherOne(): void
    {
        (new TypeParser())->withFeatures(lists: false, offsets: false, nullables: false);

        self::assertInstanceOf(TypesListTypeNode::class, (new TypeParser())->parse('int[]'));
    }
}
