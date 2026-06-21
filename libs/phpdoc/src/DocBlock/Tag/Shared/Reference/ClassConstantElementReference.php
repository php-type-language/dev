<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\Shared\Reference;

use TypeLang\Type\Name;

/**
 * Related to any internal class property reference
 */
final class ClassConstantElementReference extends ClassElementReference
{
    public function __construct(
        Name $class,
        /**
         * @var non-empty-string
         */
        public readonly string $constant,
    ) {
        parent::__construct($class);
    }
}
