<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends GenericTypeNode<TypeStatement>
 */
class NullableTypeNode extends GenericTypeNode {}
