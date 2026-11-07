<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

enum StringLiteralKind
{
    case SingleQuoted;
    case DoubleQuoted;

    public const DEFAULT = self::SingleQuoted;
}
