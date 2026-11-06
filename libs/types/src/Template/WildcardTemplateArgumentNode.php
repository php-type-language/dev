<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

/**
 * A template argument that is left unsaid, written as an asterisk.
 *
 * ```
 *  Some\Any<*>
 *  //       ^ an unspecified argument
 * ```
 *
 * The node names no type and carries no variance.
 */
final class WildcardTemplateArgumentNode extends TemplateArgumentNode {}
