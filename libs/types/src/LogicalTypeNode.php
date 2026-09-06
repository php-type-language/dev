<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * @template T of TypeNode = TypeNode
 *
 * @template-implements \IteratorAggregate<array-key, T>
 */
abstract class LogicalTypeNode extends TypeNode implements
    \IteratorAggregate,
    \Countable
{
    /**
     * @var non-empty-list<T>
     */
    public array $statements;

    /**
     * A logical statement accepts an arbitrary number of types, so there is no
     * place left for an offset argument: use the {@see $offset} property.
     *
     * @param iterable<T> $statements
     * @param int<0, max> $offset
     * @throws \LogicException in case of less than two statements are passed
     */
    public function __construct(
        iterable $statements,
        int $offset = 0,
    ) {
        $statements = self::unwrap($statements);

        if (\count($statements) < 2) {
            throw new \LogicException('A logical statement must contain at least 2 elements');
        }

        // @phpstan-ignore-next-line : List of types contains at least 2 elements
        $this->statements = $statements;

        parent::__construct($offset);
    }

    /**
     * Flattens the statements of the same kind into a single list, so that
     * a logical statement never contains a statement of its own kind.
     *
     * @param iterable<TypeNode> $statements
     * @return list<TypeNode>
     */
    private static function unwrap(iterable $statements): array
    {
        $result = [];

        foreach ($statements as $statement) {
            if ($statement instanceof static) {
                foreach (self::unwrap($statement->statements) as $child) {
                    $result[] = $child;
                }

                continue;
            }

            $result[] = $statement;
        }

        return $result;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->statements);
    }

    /**
     * @return int<2, max> a logical statement must contain at least 2 elements
     */
    public function count(): int
    {
        /** @var int<2, max> */
        return \count($this->statements);
    }

    /**
     * @return array{non-empty-list<T>, int<0, max>}
     */
    public function __serialize(): array
    {
        return [$this->statements, $this->offset];
    }

    /**
     * @param array{0?: non-empty-list<T>, 1?: int<0, max>} $data
     * @throws \UnexpectedValueException
     */
    public function __unserialize(array $data): void
    {
        $this->statements = $data[0] ?? throw new \UnexpectedValueException(\sprintf(
            'Unable to unserialize %s statements',
            static::class,
        ));

        $this->offset = $data[1] ?? 0;
    }
}
