<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\DocBlockReader;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PhpDocFactory;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyContext;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyTypeProviderInterface;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader\PropertyTypes;
use TypeLang\PhpDoc\DocBlockParser;

/**
 * Overrides types of the given properties with types from docblocks and
 * adds no properties. Types are taken by priority: on PHP 8.4 and later
 * `@return` of the `get` hook and `@param` of the `set` hook, then `@var`
 * of the property, then `@param` of the constructor for a promoted
 * property. A property with the `set` hook takes the write type from this
 * hook only. A tag is looked up by the configured names, an earlier name
 * wins over a later one. An unparsable `@param` of the constructor is an
 * error of each promoted property without a tag naming it. Names in the
 * types are resolved by the imports of the file declaring the property.
 * Requires the `type-lang/phpdoc` package.
 */
final class PhpDocMetadataReader implements MetadataReaderInterface
{
    private readonly DocBlockParser $parser;

    /**
     * @var non-empty-list<PropertyTypeProviderInterface>
     */
    private readonly array $providers;

    private readonly SourceContextFactory $contexts;

    /**
     * @param list<non-empty-string> $varTags names of `@var` tags
     * @param list<non-empty-string> $paramTags names of `@param` tags
     * @param list<non-empty-string> $returnTags names of `@return` tags
     * @throws MissingDependencyException in case of the `type-lang/phpdoc`
     *         package is not installed
     */
    public function __construct(
        array $varTags = ['phpstan-var', 'psalm-var', 'var'],
        array $paramTags = ['phpstan-param', 'psalm-param', 'param'],
        array $returnTags = ['phpstan-return', 'psalm-return', 'return'],
    ) {
        if (!self::isAvailable()) {
            throw MissingDependencyException::becausePackageIsNotInstalled('type-lang/phpdoc', 'PhpDoc reader');
        }

        $factory = PhpDocFactory::fromTagNames($varTags, $paramTags, $returnTags);

        $this->parser = $factory->createParser();
        $this->providers = $factory->createProviders();
        $this->contexts = new SourceContextFactory();
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
        $docBlocks = new DocBlockReader($this->parser);

        foreach ($metadata->properties as $name => $property) {
            if (!$class->hasProperty($name)) {
                continue;
            }

            $context = PropertyContext::create($class, $class->getProperty($name), $docBlocks, $this->contexts);

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
