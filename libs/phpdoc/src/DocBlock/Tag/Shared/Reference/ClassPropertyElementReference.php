<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\Shared\Reference;

use TypeLang\Type\Name;

/**
 * Related to any internal class property reference
 */
final class ClassPropertyElementReference extends ClassElementReference
{
    public function __construct(
        Name $class,
        /**
         * @var non-empty-string
         */
        public readonly string $property,
    ) {
        parent::__construct($class);
    }
}
