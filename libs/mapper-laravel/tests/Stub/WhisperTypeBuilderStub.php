<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests\Stub;

use TypeLang\Mapper\Context\BuildContext;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;
use TypeLang\Mapper\Type\StringType;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;

/**
 * Builds `whisper` as `string`.
 *
 * @template-implements TypeBuilderInterface<string>
 */
final class WhisperTypeBuilderStub implements TypeBuilderInterface
{
    public function tryBuild(TypeNode $node, BuildContext $context): ?StringType
    {
        if (!$node instanceof NamedTypeNode || $node->name->toString() !== 'whisper') {
            return null;
        }

        return new StringType();
    }
}
