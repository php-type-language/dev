<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt;

/**
 * @template T of TypeStatement = TypeStatement
 * @template-extends LogicalTypeNode<T>
 */
class UnionTypeNode extends LogicalTypeNode {}
