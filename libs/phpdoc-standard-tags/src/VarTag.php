<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Standard;

use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\PHPDoc\Tag\OptionalVariableNameProviderInterface;
use TypeLang\PHPDoc\Tag\Tag;
use TypeLang\PHPDoc\Tag\TypeProviderInterface;

/**
 * The "`@var`" tag defines which type of data is represented by the value of a
 * constant, property or variable.
 *
 * Each constant or property definition or variable where the type is ambiguous
 * or unknown SHOULD be preceded by a DocBlock containing the "`@var`" tag. Any
 * other variable MAY be preceded with a DocBlock containing the "`@var`" tag.
 *
 * The "`@var`" tag MUST contain the name of the element it documents. An
 * exception to this is when a declaration only refers to a single property or
 * constant. In that case, the name of the property or constant MAY be omitted.
 *
 * The name is used when compound statements are used to define a series of
 * constants or properties. Such a compound statement can only have one DocBlock
 * while several items are represented.
 *
 * ```
 *
 * * @var [<Type>] $<name> [<description>]
 * ```
 */
class VarTag extends Tag implements
    TypeProviderInterface,
    OptionalVariableNameProviderInterface
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|null $varName
     */
    public function __construct(
        string $name,
        protected readonly TypeStatement $type,
        protected readonly ?string $varName = null,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct($name, $description);
    }

    public function getType(): TypeStatement
    {
        return $this->type;
    }

    public function getVariableName(): ?string
    {
        return $this->varName;
    }
}
