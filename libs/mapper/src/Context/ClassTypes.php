<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Context;

use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Type\Repository\TypeRepositoryInterface;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;

/**
 * Types of the classes of the values met at runtime, such as the type of an
 * object in `mixed`. The type of a class is built on its first value and
 * kept.
 *
 * ```
 * $normalized = $context->types->get($object::class)->normalize($object, $context);
 * ```
 */
final class ClassTypes
{
    /**
     * @var array<class-string, TypeInterface<mixed>>
     */
    private array $types = [];

    public function __construct(
        private readonly TypeRepositoryInterface $repository,
    ) {}

    /**
     * @param class-string $class
     * @return TypeInterface<mixed>
     * @throws TypeDefinitionException in case of the type of the class
     *         cannot be built
     */
    public function get(string $class): TypeInterface
    {
        return $this->types[$class] ??= $this->repository->getType(self::createNode($class));
    }

    /**
     * Returns the node of the class, named with its namespace.
     *
     * @param class-string $class
     */
    private static function createNode(string $class): NamedTypeNode
    {
        $words = \explode('\\', $class);

        /** @var non-empty-string */
        $last = \array_pop($words);

        if ($words === []) {
            return new NamedTypeNode(new Name($last));
        }

        return new NamedTypeNode(new Name(
            $last,
            new NamespaceName(\implode('\\', $words)),
        ));
    }
}
