<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Tests\Reader;

use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\NamingStrategyMetadataReader;
use TypeLang\Mapper\Mapping\Tests\TestCase;
use TypeLang\Mapper\Naming\NamingStrategy;

final class NamingStrategyMetadataReaderTest extends TestCase
{
    public function testStrategyNamesOnlyPropertiesWithoutExternalName(): void
    {
        $metadata = (new ClassMetadata(\stdClass::class))
            ->withProperty(new PropertyMetadata('displayName'))
            ->withProperty((new PropertyMetadata('userId'))->withExternalName('id'));

        $result = (new NamingStrategyMetadataReader(NamingStrategy::SnakeCase))
            ->read(new \ReflectionClass(\stdClass::class), $metadata);

        self::assertSame('display_name', $result->findProperty('displayName')?->getOutputName());
        self::assertSame('id', $result->findProperty('userId')?->getOutputName());
    }

    public function testFirstReaderAddsNoProperties(): void
    {
        $result = (new NamingStrategyMetadataReader(NamingStrategy::SnakeCase))
            ->read(new \ReflectionClass(\stdClass::class));

        self::assertSame(\stdClass::class, $result->name);
        self::assertSame([], $result->properties);
    }
}
