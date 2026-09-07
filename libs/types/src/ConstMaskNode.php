<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A global constant referenced by a name written in part.
 *
 * ```
 *  Some\Any\SOME_*
 *  ^^^^^^^^        the namespace the constant belongs to
 *           ^^^^^^ the mask its name is written as
 *
 *  *_SOME
 *  ^^^^^^ a mask with no namespace at all
 * ```
 */
final class ConstMaskNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The name of the constant, written in part.
         */
        public MaskNode $mask,
        /**
         * The namespace the constant belongs to, the leading separator of which
         * says whether the reference is fully qualified.
         *
         * A {@see bool} stands for that separator alone, in case of the
         * constant is written with no namespace.
         */
        public Name|bool $namespaceOrFullyQualified = Name::IS_FULLY_QUALIFIED_DEFAULT_VALUE,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    /**
     * Gets whether the reference is to be read from the root namespace.
     */
    public function isFullyQualified(): bool
    {
        $context = $this->namespaceOrFullyQualified;

        if ($context instanceof Name) {
            return $context->isFullyQualified;
        }

        return $context;
    }

    /**
     * A helper method to set whether the reference is to be read from
     * the root namespace.
     */
    public function setFullyQualified(bool $isFullyQualified = true): void
    {
        $context = $this->namespaceOrFullyQualified;

        if ($context instanceof Name) {
            $this->namespaceOrFullyQualified = $isFullyQualified
                ? $context->toFullQualified()
                : $context->toUnqualified();

            return;
        }

        $this->namespaceOrFullyQualified = $isFullyQualified;
    }
}
