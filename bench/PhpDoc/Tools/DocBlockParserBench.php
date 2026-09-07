<?php

declare(strict_types=1);

namespace TypeLang\Bench\PhpDoc\Tools;

abstract class DocBlockParserBench
{
    /**
     * @var int<1, max>
     */
    private const REPEATS = 16;

    /**
     * @return iterable<non-empty-string, array{docblock: non-empty-string}>
     */
    public static function docBlocksDataProvider(): iterable
    {
        yield 'empty' => ['docblock' => '/** */'];

        yield 'inline description' => ['docblock' => '/** An example of the inline description. */'];

        yield 'inline tag' => ['docblock' => '/** @return void */'];

        yield 'description' => ['docblock' => <<<'DOC'
            /**
             * The `@link` tag can be used to define a relation, or link, between
             * the element, or part of the long description when used inline, to a URI.
             */
            DOC];

        yield 'description long' => ['docblock' => self::createDescription(self::REPEATS)];

        yield 'scalar types' => ['docblock' => <<<'DOC'
            /**
             * @param int $offset
             * @param string $name
             * @param bool $strict
             * @param float $ratio
             * @param mixed $context
             * @return void
             */
            DOC];

        yield 'union types' => ['docblock' => <<<'DOC'
            /**
             * @param int|string $key
             * @param string|null $name
             * @param ?iterable $items
             * @param bool|int|float|string|null $scalar
             * @return array|false
             */
            DOC];

        yield 'intersection types' => ['docblock' => <<<'DOC'
            /**
             * @param \Traversable&\Countable $items
             * @param (\Stringable&\JsonSerializable)|null $value
             * @param \IteratorAggregate&\ArrayAccess&\Countable $collection
             * @return \Traversable&\Countable
             */
            DOC];

        yield 'named types' => ['docblock' => <<<'DOC'
            /**
             * @param \TypeLang\PhpDoc\DocBlockParserInterface $parser
             * @param DocBlock\Tag\TagInterface $tag
             * @param self $self
             * @param static $static
             * @return $this
             */
            DOC];

        yield 'generic types' => ['docblock' => <<<'DOC'
            /**
             * @param array<int, string> $list
             * @param iterable<array-key, non-empty-string> $items
             * @param \Traversable<int, array<string, list<int>>> $nested
             * @param class-string<\Throwable> $exception
             * @return \Generator<int, string, mixed, list<non-empty-string>>
             */
            DOC];

        yield 'shape types' => ['docblock' => <<<'DOC'
            /**
             * @param array{int, string, bool} $tuple
             * @param array{name: string, age?: int<0, max>} $struct
             * @param array{
             *     id: positive-int,
             *     meta: array{
             *         created: \DateTimeInterface,
             *         tags: list<non-empty-string>,
             *         ...
             *     }
             * } $nested
             * @param object{name: string, value?: mixed} $object
             * @return array{}
             */
            DOC];

        yield 'callable types' => ['docblock' => <<<'DOC'
            /**
             * @param callable $any
             * @param callable(): void $empty
             * @param callable(int, string): bool $simple
             * @param \Closure(non-empty-string, mixed...): (int|string) $variadic
             * @param callable(callable(int): string): callable(string): int $nested
             * @return \Closure(): \Generator<int, string>
             */
            DOC];

        yield 'literal types' => ['docblock' => <<<'DOC'
            /**
             * @param 'read'|'write'|'append' $mode
             * @param 1|2|3|42 $version
             * @param 0.1|-0.5 $ratio
             * @param true|false $flag
             * @param "double \"quoted\" literal" $quoted
             * @return 'ok'
             */
            DOC];

        yield 'const types' => ['docblock' => <<<'DOC'
            /**
             * @param \PDO::FETCH_* $mode
             * @param self::STATUS_ACTIVE|self::STATUS_INACTIVE $status
             * @param \PHP_INT_MAX $max
             * @param int<0, \PHP_INT_MAX> $range
             * @return static::DEFAULT_VALUE
             */
            DOC];

        yield 'conditional types' => ['docblock' => <<<'DOC'
            /**
             * @template T
             * @param T $value
             * @return (T is int ? string : (T is string ? int : bool))
             */
            DOC];

        yield 'template tags' => ['docblock' => <<<'DOC'
            /**
             * @template TKey of array-key
             * @template TValue of object
             * @template-covariant TResult
             * @extends \IteratorAggregate<TKey, TValue>
             * @implements \ArrayAccess<TKey, TValue>
             * @param TKey $key
             * @param TValue $value
             * @return static<TKey, TValue>
             */
            DOC];

        yield 'inline tags' => ['docblock' => <<<'DOC'
            /**
             * {@inheritDoc}
             *
             * See the {@see \TypeLang\PhpDoc\DocBlockParser::parse()} method and
             * the {@link https://www.ietf.org/rfc/rfc2396.txt RFC2396} document.
             *
             * @return void
             */
            DOC];

        yield 'metadata tags' => ['docblock' => <<<'DOC'
            /**
             * @author Nesmeyanov Kirill <nesk@xakep.ru>
             * @copyright 2024 TypeLang
             * @license MIT
             * @since 1.0
             * @deprecated since 2.0, use something else instead
             * @internal
             * @api
             * @final
             * @todo Remove this method
             */
            DOC];

        yield 'unknown tags' => ['docblock' => <<<'DOC'
            /**
             * @some-vendor-tag with an arbitrary payload
             * @another_tag(with, parens)
             * @x-custom {"json": "like", "payload": [1, 2, 3]}
             * @return void
             */
            DOC];

        yield 'real world' => ['docblock' => <<<'DOC'
            /**
             * The `@link` tag can be used to define a relation, or link, between
             * the element, or part of the long description when used inline, to a URI.
             *
             * ```
             * "@link" [<URI> | <reference>] [<description>]
             * ```
             *
             * @link https://www.ietf.org/rfc/rfc2396.txt RFC2396
             * @return iterable<string, array{
             *     CommentParserInterface,
             *     string,
             *     list<array{ as: string, super: int<0, max> }>
             * }>
             */
            DOC];

        yield 'many params' => ['docblock' => self::createTags(self::REPEATS, [
            '@param int<0, max> $offset%d',
            '@param non-empty-string $name%d',
            '@param list<array{id: int, name: string}> $items%d',
        ])];

        yield 'many throws' => ['docblock' => self::createTags(self::REPEATS, [
            '@throws \RuntimeException in case of a %d error',
            '@throws \InvalidArgumentException in case of an invalid #%d argument',
        ])];

        yield 'many mixed tags' => ['docblock' => self::createTags(self::REPEATS, [
            '@param \Closure(int, string...): (bool|null) $callback%d',
            '@param array{a: int, b: array{c: string, d?: list<float>}} $shape%d',
            '@var iterable<array-key, \Traversable<int, non-empty-string>> $iterable%d',
            '@throws \LogicException on the %d failure',
            '@see \TypeLang\PhpDoc\DocBlockParser::parse() the #%d reference',
            '@deprecated since %d.0',
        ])];

        yield 'huge' => ['docblock' => self::createHuge()];
    }

    /**
     * @param int<1, max> $repeats
     * @return non-empty-string
     */
    private static function createDescription(int $repeats): string
    {
        $lines = ['/**'];

        for ($i = 0; $i < $repeats; ++$i) {
            $lines[] = ' * Lorem ipsum dolor sit amet, consectetur adipiscing elit,';
            $lines[] = ' * sed do eiusmod tempor incididunt ut labore et dolore magna.';
            $lines[] = ' *';
        }

        $lines[] = ' */';

        return \implode("\n", $lines);
    }

    /**
     * @param int<1, max> $repeats
     * @param non-empty-list<non-empty-string> $templates sprintf templates of the tag lines
     * @return non-empty-string
     */
    private static function createTags(int $repeats, array $templates): string
    {
        $lines = ['/**'];

        for ($i = 0; $i < $repeats; ++$i) {
            foreach ($templates as $template) {
                $lines[] = ' * ' . \sprintf($template, $i);
            }
        }

        $lines[] = ' */';

        return \implode("\n", $lines);
    }

    /**
     * @return non-empty-string
     */
    private static function createHuge(): string
    {
        // Removes the trailing " */" line terminator of the description block.
        $description = \substr(self::createDescription(self::REPEATS), 0, -3);

        // Removes the leading "/**" line opener of the tags block.
        $tags = \substr(self::createTags(self::REPEATS, [
            '@param \Closure(int, string...): (bool|null) $callback%d',
            '@param array{a: int, b: array{c: string, d?: list<float>}} $shape%d',
            '@throws \LogicException on the %d failure',
        ]), 4);

        return $description . $tags;
    }

    /**
     * @param array{docblock: non-empty-string} $params
     */
    abstract public function benchParseDocBlock(array $params): void;
}
