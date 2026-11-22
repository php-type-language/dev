<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Tools;

use PhpBench\Attributes\Groups;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataBuilder;

#[Groups(['typelang', 'baseline'])]
final class TypeLangMapperBench extends MapperToolBench
{
    private Mapper $mapper;

    protected function boot(?string $cache): void
    {
        if ($cache === null) {
            $this->mapper = new Mapper();

            return;
        }

        $this->mapper = new Mapper(
            metadata: MetadataBuilder::createDefault()
                ->withPsr6Cache(new PhpFilesAdapter(directory: $cache))
                ->build(),
        );
    }

    protected function denormalize(mixed $data, string $type): mixed
    {
        return $this->mapper->denormalize($data, $type);
    }

    protected function normalize(mixed $value, string $type): mixed
    {
        return $this->mapper->normalize($value, $type);
    }
}
