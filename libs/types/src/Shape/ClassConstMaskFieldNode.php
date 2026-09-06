<?php

declare(strict_types=1);

namespace TypeLang\Type\Shape;

use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\TypeNode;

/**
 * @template-extends ExplicitFieldNode<ClassConstMaskNode>
 */
final class ClassConstMaskFieldNode extends ExplicitFieldNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        ClassConstMaskNode $key,
        TypeNode $type,
        bool $isOptional = false,
        int $offset = 0,
    ) {
        parent::__construct(
            key: $key,
            type: $type,
            isOptional: $isOptional,
            offset: $offset,
        );
    }

    public function getIndex(): string
    {
        return \vsprintf('%s::%s*', [
            $this->key->class->toString(),
            $this->key->constant?->toString(),
        ]);
    }
}
