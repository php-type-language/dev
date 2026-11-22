<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Tools;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use PhpBench\Attributes\Groups;

#[Groups(['jms'])]
final class JmsSerializerBench extends MapperToolBench
{
    protected const string LIST_TYPE = 'array<%s>';

    private ArrayTransformerInterface $serializer;

    protected function boot(?string $cache): void
    {
        // The keys of the data are the names of the properties
        $builder = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()),
            );

        if ($cache !== null) {
            $builder = $builder->setCacheDir($cache);
        }

        $this->serializer = $builder->build();
    }

    protected function denormalize(mixed $data, string $type): mixed
    {
        return $this->serializer->fromArray($data, $type);
    }

    protected function normalize(mixed $value, string $type): mixed
    {
        return $this->serializer->toArray($value, type: $type);
    }
}
