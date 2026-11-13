<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\HookTypeProvider;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PromotedParamTypeProvider;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyContext;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyTypeProviderInterface;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyTypes;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\VarTagTypeProvider;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\Platform\StandardPlatform;

/**
 * Overrides types of the given properties with types from docblocks and
 * adds no properties.
 *
 * Note: Requires the `type-lang/phpdoc` package.
 */
final class PhpDocMetadataReader implements MetadataReaderInterface
{
    /**
     * @var non-empty-list<PropertyTypeProviderInterface>
     */
    private readonly array $providers;

    private readonly SourceContextFactory $contexts;

    public function __construct(
        private readonly DocBlockParser $parser = new DocBlockParser([
            new StandardPlatform(),
        ]),
    ) {
        $this->providers = self::createProviders();
        $this->contexts = new SourceContextFactory();
    }

    /**
     * @return non-empty-list<PropertyTypeProviderInterface>
     */
    private static function createProviders(): array
    {
        $providers = [];

        if (\PHP_VERSION_ID >= 80400) {
            $providers[] = new HookTypeProvider();
        }

        $providers[] = new VarTagTypeProvider();
        $providers[] = new PromotedParamTypeProvider();

        return $providers;
    }

    /**
     * Returns {@see true} in case of the `type-lang/phpdoc` package is
     * installed.
     */
    public static function isAvailable(): bool
    {
        return \class_exists(DocBlockParser::class);
    }

    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $metadata ??= new ClassMetadata($class->getName());

        foreach ($metadata->properties as $name => $property) {
            if (!$class->hasProperty($name)) {
                continue;
            }

            $context = PropertyContext::create($class, $class->getProperty($name), $this->parser, $this->contexts);

            $metadata = $metadata->withProperty($this->readProperty($context, $property));
        }

        return $metadata;
    }

    private function readProperty(PropertyContext $context, PropertyMetadata $property): PropertyMetadata
    {
        $types = new PropertyTypes();

        foreach ($this->providers as $provider) {
            $types = $types->withLower($provider->readTypes($context));

            if ($types->isComplete()) {
                break;
            }
        }

        if ($types->readType !== null) {
            $property = $property->withReadType($types->readType);
        }

        if ($types->writeType !== null) {
            $property = $property->withWriteType($types->writeType);
        }

        return $property;
    }
}
