<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Tools;

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\ArrayNormalizer;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use PhpBench\Attributes\Groups;

#[Groups(['valinor'])]
final class ValinorMapperBench extends MapperToolBench
{
    private TreeMapper $mapper;

    private ArrayNormalizer $normalizer;

    protected function boot(?string $cache): void
    {
        $mapper = new MapperBuilder();
        $normalizer = new NormalizerBuilder();

        if ($cache !== null) {
            $mapper = $mapper->withCache(new FileSystemCache($cache));
            $normalizer = $normalizer->withCache(new FileSystemCache($cache));
        }

        $this->mapper = $mapper->mapper();
        $this->normalizer = $normalizer->normalizer(Format::array());
    }

    protected function denormalize(mixed $data, string $type): mixed
    {
        return $this->mapper->map($type, $data);
    }

    protected function normalize(mixed $value, string $type): mixed
    {
        return $this->normalizer->normalize($value);
    }
}
