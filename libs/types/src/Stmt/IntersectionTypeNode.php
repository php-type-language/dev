<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends LogicalTypeNode<T>
 */
class IntersectionTypeNode extends LogicalTypeNode {}
