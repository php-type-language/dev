<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Tools;

use PhpBench\Attributes\Groups;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoCacheExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Mapping\Factory\CacheClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * The services of the object normalizer are wired like the framework
 * bundle wires them, the other normalizers of the bundle are left out.
 */
#[Groups(['symfony'])]
final class SymfonySerializerBench extends MapperToolBench
{
    protected const string LIST_TYPE = '%s[]';

    private Serializer $serializer;

    protected function boot(?string $cache): void
    {
        $reflection = new ReflectionExtractor();

        $metadata = new ClassMetadataFactory(new AttributeLoader());
        $accessor = new PropertyAccessor();
        $properties = new PropertyInfoExtractor(
            listExtractors: [$reflection],
            typeExtractors: [new PhpStanExtractor(), new PhpDocExtractor(), $reflection],
            accessExtractors: [$reflection],
            initializableExtractors: [$reflection],
        );

        if ($cache !== null) {
            $pool = new PhpFilesAdapter(directory: $cache);

            $metadata = new CacheClassMetadataFactory($metadata, $pool);
            $accessor = new PropertyAccessor(cacheItemPool: $pool);
            $properties = new PropertyInfoCacheExtractor($properties, $pool);
        }

        $this->serializer = new Serializer([
            new ArrayDenormalizer(),
            new ObjectNormalizer(
                classMetadataFactory: $metadata,
                propertyAccessor: $accessor,
                propertyTypeExtractor: $properties,
            ),
        ]);
    }

    protected function denormalize(mixed $data, string $type): mixed
    {
        return $this->serializer->denormalize($data, $type);
    }

    protected function normalize(mixed $value, string $type): mixed
    {
        return $this->serializer->normalize($value);
    }
}
