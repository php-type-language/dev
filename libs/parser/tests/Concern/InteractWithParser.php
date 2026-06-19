<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Concern;

use PHPUnit\Framework\TestCase;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\Parser;

/**
 * @mixin TestCase
 * @phpstan-require-extends TestCase
 */
trait InteractWithParser
{
    protected Parser $parser {
        get => $this->parser ??= new Parser();
    }

    protected function parseTypeStatement(string $statement): ?TypeStatement
    {
        return $this->parser->parse($statement);
    }
}
