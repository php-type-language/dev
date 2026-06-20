<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 */
abstract class GenericTypeNode extends TypeStatement
{
    /**
     * @param T $type
     */
    public function __construct(
        public TypeStatement $type,
    ) {}
}
