<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\HookTypeProvider;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\MapperPlatform;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PromotedParamTypeProvider;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyContext;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyTypeProviderInterface;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyTypes;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\VarTagTypeProvider;
use TypeLang\PhpDoc\DocBlockParser;

/**
 * Overrides types of the given properties with types from docblocks and
 * adds no properties.
 *
 * Note: Requires the `type-lang/phpdoc` package.
 */
final class PhpDocMetadataReader implements OptionalMetadataReaderInterface
{
    private readonly DocBlockParser $parser;

    /**
     * @var non-empty-list<PropertyTypeProviderInterface>
     */
    private readonly array $providers;

    private readonly SourceContextFactory $contexts;

    public function __construct(
        ?DocBlockParser $parser = null,
    ) {
        if (!self::isAvailable()) {
            throw MissingDependencyException::becausePackageIsNotInstalled('type-lang/phpdoc', 'PHPDoc tags');
        }

        $this->parser = $parser ?? $this->createDefaultDocBlockParser();
        $this->providers = self::createProviders();
        $this->contexts = new SourceContextFactory();
    }

    private function createDefaultDocBlockParser(): DocBlockParser
    {
        return new DocBlockParser([
            MapperPlatform::createWithDefaultTagNames(),
        ]);
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

            $context = PropertyContext::create(
                class: $class,
                reflection: $class->getProperty($name),
                parser: $this->parser,
                contexts: $this->contexts,
            );

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
