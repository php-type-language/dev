<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Stub;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class BlankFunctionProviderStub implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction(
                'blank',
                static fn(string $value): string => "({$value} === '')",
                static fn(array $values, mixed $value): bool => $value === '',
            ),
        ];
    }
}
