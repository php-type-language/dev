<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\NamespaceName\NamespaceKind;

/**
 * A namespace a name or a mask of constants belongs to.
 *
 * ```
 *  Some\Any\Name
 *  ^^^^^^^^      a qualified namespace
 *
 *  \Some\Any\Name
 *  ^^^^^^^^^      a fully qualified namespace begins in a separator
 *
 *  namespace\Some\Name
 *  ^^^^^^^^^^^^^^      a namespace relative to the current one
 * ```
 *
 * The class cannot be named `Namespace`, the word is reserved by PHP.
 */
final class NamespaceName extends Node implements \Stringable
{
    /**
     * @var non-empty-string
     */
    public const DELIMITER = '\\';

    /**
     * @var non-empty-string
     */
    private const RELATIVE_NAMESPACE = 'namespace';

    /**
     * @var non-empty-string
     */
    private const RELATIVE_NAMESPACE_PREFIX = self::RELATIVE_NAMESPACE
        . self::DELIMITER;

    /**
     * The words of the namespace with the separators between them and with
     * no one at the ends. The `namespace` keyword and the leading separator
     * are not the words.
     */
    public readonly string $value;

    /**
     * From where the namespace is read.
     */
    public readonly NamespaceKind $kind;

    /**
     * A qualified namespace that begins in the `namespace` keyword turns into
     * a relative one to the current namespace, the keyword is dropped.
     *
     * ```
     *  new NamespaceName('namespace\Some')  // Some, from the current one
     *  new NamespaceName('namespace')       // no words, from the current one
     * ```
     *
     * @param int<0, max> $offset
     */
    public function __construct(
        string $value = '',
        NamespaceKind $kind = NamespaceKind::Qualified,
        int $offset = 0,
    ) {
        if ($kind === NamespaceKind::Qualified) {
            if (\strncasecmp($value, self::RELATIVE_NAMESPACE_PREFIX, 10) === 0) {
                $kind = NamespaceKind::NamespaceRelative;
                $value = \substr($value, 10);
            } elseif (\strcasecmp($value, self::RELATIVE_NAMESPACE) === 0) {
                $kind = NamespaceKind::NamespaceRelative;
                $value = '';
            }
        }

        $this->value = $value;
        $this->kind = $kind;

        parent::__construct($offset);
    }

    /**
     * Whether the namespace is the root one: the leading separator and no
     * words.
     *
     * @api
     */
    public function isRoot(): bool
    {
        return $this->kind === NamespaceKind::FullyQualified
            && $this->value === '';
    }

    /**
     * Gets the words of the namespace without the separators.
     *
     * ```
     *  \Some\Any  // ['Some', 'Any']
     *  \          // []
     * ```
     *
     * @api
     *
     * @return list<non-empty-string>
     */
    public function toArray(): array
    {
        if ($this->value === '') {
            return [];
        }

        /** @var non-empty-list<non-empty-string> */
        return \explode(self::DELIMITER, $this->value);
    }

    /**
     * Returns the namespace as it is written, with the leading separator or
     * the keyword if it has one and with no separator at the end.
     *
     * ```
     *  Some\Any            // Some\Any
     *  \Some\Any           // \Some\Any
     *  \                   // \
     *  namespace\Some\Any  // namespace\Some\Any
     *  namespace           // namespace
     * ```
     *
     * @api
     */
    public function toString(): string
    {
        if ($this->kind === NamespaceKind::FullyQualified) {
            return self::DELIMITER . $this->value;
        }

        if ($this->kind === NamespaceKind::NamespaceRelative) {
            if ($this->value === '') {
                return self::RELATIVE_NAMESPACE;
            }

            return self::RELATIVE_NAMESPACE_PREFIX . $this->value;
        }

        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
