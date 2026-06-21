<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\Shared\Reference;

use TypeLang\Type\TypeNode;

/**
 * Related to internal type reference
 */
final class TypeElementReference extends ElementReference
{
    public function __construct(
        public readonly TypeNode $type,
    ) {}
}
