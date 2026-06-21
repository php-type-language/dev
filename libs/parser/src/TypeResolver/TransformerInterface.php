<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

use TypeLang\Node\Name;

interface TransformerInterface
{
    public function __invoke(Name $name): ?Name;
}
