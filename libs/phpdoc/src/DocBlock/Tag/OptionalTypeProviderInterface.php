<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Tag;

use TypeLang\Type\TypeNode;

/**
 * Representation of any entry that MAY contain optional type definition.
 */
interface OptionalTypeProviderInterface
{
    /**
     * Gets an AST object of the {@see TypeNode} type or {@see null}
     * in case the type is not specified.
     *
     * @readonly
     */
    public ?TypeNode $type { get; }
}
