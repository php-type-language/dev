<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class BlankExpressionLanguageStub extends ExpressionLanguage
{
    protected function registerFunctions(): void
    {
        parent::registerFunctions();

        $this->register(
            'blank',
            static fn(string $value): string => "({$value} === '')",
            static fn(array $values, mixed $value): bool => $value === '',
        );
    }
}
