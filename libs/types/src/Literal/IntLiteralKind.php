<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

enum IntLiteralKind
{
    case Binary;
    case Octal;
    case Decimal;
    case Hexadecimal;

    public const DEFAULT = self::Decimal;
}
