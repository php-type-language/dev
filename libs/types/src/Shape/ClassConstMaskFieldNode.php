<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<ClassConstMaskNode>
 */
final class ClassConstMaskFieldNode extends ExplicitFieldNode
{
    public function __construct(
        ClassConstMaskNode $key,
        TypeNode $type,
        bool $isOptional = false,
        ?AttributeGroupListNode $attributes = null,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            attributes: $attributes,
        );
    }

    public function index(): string
    {
        return \vsprintf('%s::%s*', [
            $this->key->class->toString(),
            $this->key->constant?->toString(),
        ]);
    }
}
