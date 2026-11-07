<?php

declare(strict_types=1);

namespace TypeLang\Printer\NativeTypePrinter;

use TypeLang\Type\NamedTypeNode;
use TypeLang\TypeSystem\Normalizer;
use TypeLang\TypeSystem\Registry;
use TypeLang\TypeSystem\Resolver;

/**
 * The map of the names of virtual types to the native types they stand for.
 *
 * The names added by hand win. The builtin names are resolved by the
 * {@see Resolver} of the type system, which knows what the types of
 * PHP, PHPStan, Psalm and Phan stand for.
 */
final class TypeAliases
{
    /**
     * @var array<non-empty-lowercase-string, non-empty-string>
     */
    private array $aliases = [];

    /**
     * The native types the builtin names stand for, to resolve each once.
     *
     * @var array<non-empty-lowercase-string, non-empty-string>
     */
    private array $resolved = [];

    private readonly Normalizer $normalizer;

    private readonly Resolver $resolver;

    public function __construct(?Registry $registry = null)
    {
        $this->normalizer = new Normalizer($registry ?? new Registry());
        $this->resolver = new Resolver();
    }

    /**
     * @param non-empty-string $alias
     * @param non-empty-string $type
     */
    public function add(string $alias, string $type): void
    {
        $this->aliases[\strtolower($alias)] = $type;
    }

    /**
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addUnion(string $alias, array $types): void
    {
        \sort($types);

        $this->add($alias, \implode('|', $types));
    }

    /**
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addIntersection(string $alias, array $types): void
    {
        \sort($types);

        $this->add($alias, \implode('&', $types));
    }

    /**
     * Gets the type a name stands for, or the name itself in case of it has
     * no alias.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public function resolve(string $name): string
    {
        $key = \strtolower($name);

        // The union printer collapses them into a bool
        if (\in_array($key, ['true', 'false', '\true', '\false'], true)) {
            return $this->aliases[$key] ?? $name;
        }

        return $this->aliases[$key] ?? $this->resolved[$key] ??= $this->resolver->print(
            $this->normalizer->normalize(NamedTypeNode::from($name)),
        );
    }
}
