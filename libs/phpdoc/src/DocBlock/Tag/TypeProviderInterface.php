<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Tag;

use TypeLang\Type\TypeNode;

/**
 * Representation of any entry that contain type definition.
 */
interface TypeProviderInterface extends OptionalTypeProviderInterface
{
    /**
     * Gets an AST object of the {@see TypeNode} type.
     */
    public TypeNode $type { get; }
}
