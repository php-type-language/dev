<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Naming\NamingStrategyInterface;

/**
 * Names the given properties without an explicit external name by the
 * naming strategy and adds no properties.
 */
final class NamingStrategyMetadataReader implements MetadataReaderInterface
{
    public function __construct(
        private readonly NamingStrategyInterface $strategy,
    ) {}

    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $metadata ??= new ClassMetadata($class->getName());

        foreach ($metadata->properties as $property) {
            if (!$property->isExternalNameExplicit) {
                $metadata = $metadata->withProperty(
                    $property->withExternalName($this->strategy->rename($property->name)),
                );
            }
        }

        return $metadata;
    }
}
