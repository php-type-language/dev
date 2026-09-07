<?php

declare(strict_types=1);

namespace TypeLang\Type\Condition;

/**
 * Whether the subject is anything but the target.
 *
 * ```
 *  ($value is not int ? string : bool)
 * ```
 */
final class NotEqualConditionNode extends Condition {}
