<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Node;
use TypeLang\Type\Template\VarianceNode\VarianceKind;

/**
 * A variance of a template argument or of a template parameter, the word that
 * stands in front of it.
 *
 * ```
 *  Some\Any<covariant string>
 *  //       ^^^^^^^^^ the variance
 *
 *  callable<contravariant T>(T): void
 *  //       ^^^^^^^^^^^^^ the variance
 * ```
 */
final class VarianceNode extends Node
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The kind of the variance.
         */
        public readonly VarianceKind $kind,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
