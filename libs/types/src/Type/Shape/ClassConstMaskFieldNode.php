<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Shape;

use TypeLang\Node\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Node\Type\ClassConstMaskNode;
use TypeLang\Node\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<ClassConstMaskNode>
 */
class ClassConstMaskFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => \vsprintf('%s::%s*', [
            $this->key->class->toString(),
            $this->key->constant?->toString(),
        ]);
    }

    public function __construct(
        ClassConstMaskNode $key,
        TypeNode $type,
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
