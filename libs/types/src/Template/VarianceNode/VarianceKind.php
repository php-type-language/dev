<?php

declare(strict_types=1);

namespace TypeLang\Type\Template\VarianceNode;

/**
 * The kind of a variance, it is the word the variance is written with.
 */
enum VarianceKind: string
{
    /**
     * A subtype may stand in the place of the type.
     *
     * ```
     *  Some\Any<covariant string>
     * ```
     */
    case Covariant = 'covariant';

    /**
     * A supertype may stand in the place of the type.
     *
     * ```
     *  Some\Any<contravariant string>
     * ```
     */
    case Contravariant = 'contravariant';

    /**
     * Only the type itself may stand in its place.
     *
     * ```
     *  Some\Any<invariant string>
     * ```
     */
    case Invariant = 'invariant';
}
