<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt\Shape;

use TypeLang\Type\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Type\Stmt\ClassConstMaskNode;
use TypeLang\Type\Stmt\ClassConstNode;
use TypeLang\Type\Stmt\TypeStatement;

/**
 * @template-extends ExplicitFieldNode<ClassConstMaskNode>
 */
class ClassConstMaskFieldNode extends ExplicitFieldNode
{
    public string $index {
        get {
            $result = $this->key->class->toString()
                . '::' . $this->key->constant?->toString();

            if ($this->key instanceof ClassConstNode) {
                return $result;
            }

            return $result . '*';
        }
    }

    public function __construct(
        ClassConstMaskNode $key,
        TypeStatement $type,
        bool $optional = false,
        ?AttributeGroupsListNode $attributes = null,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            optional: $optional,
            attributes: $attributes,
        );
    }
}
