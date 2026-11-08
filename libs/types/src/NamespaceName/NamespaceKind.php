<?php

declare(strict_types=1);

namespace TypeLang\Type\NamespaceName;

/**
 * How the namespace of a name is written, that is, from where it is read.
 */
enum NamespaceKind
{
    /**
     * A namespace written as it is.
     *
     * ```
     *  Some\Any\Name
     *  ^^^^^^^^
     * ```
     */
    case Qualified;

    /**
     * A namespace that begins in a separator, it is read from the root.
     *
     * ```
     *  \Some\Any\Name
     *  ^^^^^^^^^
     *
     *  \Name
     *  ^     the root namespace has no words
     * ```
     */
    case FullyQualified;

    /**
     * A namespace that begins in the `namespace` keyword, it is read from
     * the current one. The keyword is not a word of the namespace.
     *
     * ```
     *  namespace\Some\Name
     *  ^^^^^^^^^^^^^^
     *
     *  namespace\Name
     *  ^^^^^^^^^      the current namespace has no words
     * ```
     */
    case NamespaceRelative;
}
