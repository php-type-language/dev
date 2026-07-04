<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar;

use TypeLang\PhpDoc\Parser\Grammar\Exception\InvalidTagRuleException;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;

/**
 * The named terminals ({@see MatchRule}) that tag definitions may reference.
 *
 * A reader reads its value from the {@see Cursor} and returns it, or throws a
 * {@see NoMatchException} when the input does not fit.
 *
 * ```
 * $grammar->addRule('URI', static function (Cursor $cursor): string {
 *     $uri = $cursor->readWord();
 *
 *     if ($uri === '') {
 *         throw new NoMatchException('Expected a URI');
 *     }
 *
 *     return $uri;
 * });
 * ```
 *
 * @phpstan-type RuleType callable(Cursor): mixed
 *
 * @template-implements \IteratorAggregate<non-empty-string, RuleType>
 */
final class Grammar implements \Countable, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, RuleType>
     */
    private array $rules = [];

    /**
     * @param iterable<non-empty-string, RuleType> $rules
     */
    public function __construct(iterable $rules = [])
    {
        $this->rules = \iterator_to_array($rules);
    }

    /**
     * Registers (or overrides) a named terminal reader.
     *
     * @param non-empty-string $name
     * @param RuleType $reader
     */
    public function add(string $name, callable $reader): void
    {
        $this->rules[$name] = $reader;
    }

    /**
     * @param non-empty-string $name
     */
    public function has(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    /**
     * @param non-empty-string $name
     * @return RuleType
     * @throws InvalidTagRuleException
     */
    public function get(string $name): callable
    {
        return $this->rules[$name]
            ?? throw InvalidTagRuleException::becauseInvalidRule($name);
    }

    public function getIterator(): \Traversable
    {
        /** @var \ArrayIterator<non-empty-string, RuleType> */
        return new \ArrayIterator($this->rules);
    }

    /**
     * @return int<0, max>
     */
    public function count(): int
    {
        return \count($this->rules);
    }
}
