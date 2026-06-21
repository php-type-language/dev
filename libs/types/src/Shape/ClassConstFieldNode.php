<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\Attribute\AttributeGroupsListNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<ClassConstNode>
 */
final class ClassConstFieldNode extends ExplicitFieldNode
{
    public string $index {
        get => \vsprintf('%s::%s', [
            $this->key->class->toString(),
            $this->key->constant->toString(),
        ]);
    }

    public function __construct(
        ClassConstNode $key,
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
