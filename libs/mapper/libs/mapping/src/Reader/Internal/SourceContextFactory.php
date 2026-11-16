<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\Internal;

use TypeLang\Mapper\Mapping\Exception\MetadataException;
use TypeLang\Mapper\Mapping\Metadata\SourceInfo;
use TypeLang\Parser\TypeResolver;

/**
 * Creates the source contexts of properties of one reader.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class SourceContextFactory
{
    /**
     * Resolvers of the names written in a class or trait body by the name of
     * the class or trait, each one knows the imports of its file
     *
     * @var array<string, TypeResolver>
     */
    private array $resolvers = [];

    /**
     * Returns the class or trait whose body declares the property.
     *
     * @return \ReflectionClass<object>
     */
    public function findScope(\ReflectionProperty $property): \ReflectionClass
    {
        return self::findTraitScope($property->getDeclaringClass(), $property->name);
    }

    /**
     * Creates the place of a property declared in PHP code, the types are
     * resolved by the imports of the file declaring the property.
     *
     * @param \ReflectionClass<object> $class class the property is read from
     */
    public function createFromReflection(\ReflectionClass $class, \ReflectionProperty $property): SourceContext
    {
        $scope = $this->findScope($property);

        return new SourceContext(
            target: MetadataException::formatPropertyTarget($class->name, $property->name),
            source: SourceInfo::createFromReflection($scope),
            resolver: fn(): TypeResolver => $this->getResolver($scope),
            namespace: self::findNamespace($scope),
        );
    }

    /**
     * @param \ReflectionClass<object> $scope class or trait whose file
     *        contains the type
     */
    private function getResolver(\ReflectionClass $scope): TypeResolver
    {
        return $this->resolvers[$scope->name] ??= (new TypeResolver())
            ->withTypeImportsFromClass($scope);
    }

    /**
     * @param \ReflectionClass<object> $class
     * @return non-empty-string|null
     */
    private static function findNamespace(\ReflectionClass $class): ?string
    {
        $namespace = $class->getNamespaceName();

        if ($namespace === '' || $class->isAnonymous()) {
            return null;
        }

        return $namespace;
    }

    /**
     * @param \ReflectionClass<object> $class
     * @return \ReflectionClass<object>
     */
    private static function findTraitScope(\ReflectionClass $class, string $property): \ReflectionClass
    {
        foreach ($class->getTraits() as $trait) {
            if ($trait->hasProperty($property)) {
                return self::findTraitScope($trait, $property);
            }
        }

        return $class;
    }
}
