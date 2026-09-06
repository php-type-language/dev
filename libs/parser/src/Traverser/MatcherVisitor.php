<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Node;

/**
 * @property-read bool $isFound An alias of {@see isFound()} method.
 */
class MatcherVisitor extends Visitor
{
    /**
     * @var list<non-empty-string>
     */
    private const VIRTUAL_PROPERTIES = [
        'isFound',
    ];

    public ?Node $node = null;

    private bool $shouldContinue = false;

    /**
     * @param \Closure(Node):bool $matcher
     * @param (\Closure(Node):bool)|null $break
     */
    public function __construct(
        private readonly \Closure $matcher,
        private readonly ?\Closure $break = null,
    ) {}

    /**
     * Returns {@see true} in case of a node matching the criteria was found.
     */
    public function isFound(): bool
    {
        return $this->node !== null;
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'isFound' => $this->isFound(),
            default => throw new \OutOfRangeException(
                message: \sprintf('Undefined property %s::$%s', static::class, $name),
            ),
        };
    }

    public function __isset(string $name): bool
    {
        return \in_array($name, self::VIRTUAL_PROPERTIES, true);
    }

    public function before(): void
    {
        $this->node = null;
    }

    public function enter(Node $node): ?Command
    {
        if ($this->node !== null || $this->shouldContinue) {
            return Command::SkipChildren;
        }

        if (($this->matcher)($node)) {
            $this->shouldContinue = true;
            $this->node = $node;

            return Command::SkipChildren;
        }

        if ($this->break !== null && ($this->break)($node)) {
            $this->shouldContinue = true;

            return Command::SkipChildren;
        }

        return null;
    }
}
