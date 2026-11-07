<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\Literal\IntParser;

enum IntNumberSign
{
    case None;
    case Minus;
    case Plus;
}
