<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperAccessor;
use TypeLang\Mapper\Mapping\Metadata\Field\InputFieldMetadata;
use TypeLang\Mapper\Mapping\Metadata\Field\OutputFieldMetadata;
use TypeLang\Mapper\PropertyAccessor\PropertyReaderInterface;
use TypeLang\Mapper\PropertyAccessor\PropertyWriterInterface;
use TypeLang\Mapper\PropertyAccessor\Strategy\AccessStrategyInterface;

#[AsMapperAccessor]
final class AccessorStub implements AccessStrategyInterface
{
    public function tryCreateReader(\ReflectionClass $class, OutputFieldMetadata $field): ?PropertyReaderInterface
    {
        return null;
    }

    public function tryCreateWriter(\ReflectionClass $class, InputFieldMetadata $field): ?PropertyWriterInterface
    {
        return null;
    }
}
