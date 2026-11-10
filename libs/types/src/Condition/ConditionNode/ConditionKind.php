<?php

declare(strict_types=1);

namespace TypeLang\Type\Condition\ConditionNode;

/**
 * The kind of a condition, it is the operator the condition is written with.
 */
enum ConditionKind: string
{
    /**
     * Whether the subject is the target.
     *
     * ```
     *  ($value is int ? string : bool)
     * ```
     */
    case Equal = 'is';

    /**
     * Whether the subject is anything but the target.
     *
     * ```
     *  ($value is not int ? string : bool)
     * ```
     */
    case NotEqual = 'is not';
}
