<?php

declare(strict_types=1);

namespace TypeLang\Type\Condition;

/**
 * Whether the subject is the target.
 *
 * ```
 *  ($value is int ? string : bool)
 * ```
 */
final class EqualConditionNode extends Condition {}
