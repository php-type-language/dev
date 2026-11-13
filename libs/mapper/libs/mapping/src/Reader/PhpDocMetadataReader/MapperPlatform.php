<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTagDefinition;
use TypeLang\PhpDoc\Platform\Platform;

final class MapperPlatform extends Platform
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        private readonly string $var,
        /**
         * @var list<non-empty-string>
         */
        private readonly array $varAliases,
        /**
         * @var non-empty-string
         */
        private readonly string $return,
        /**
         * @var list<non-empty-string>
         */
        private readonly array $returnAliases,
        /**
         * @var non-empty-string
         */
        private readonly string $param,
        /**
         * @var list<non-empty-string>
         */
        private readonly array $paramAliases,
    ) {}

    public static function createWithSpecialTagNames(): self
    {
        return new self(
            var: 'type-lang-var',
            varAliases: [],
            return: 'type-lang-return',
            returnAliases: [],
            param: 'type-lang-param',
            paramAliases: [],
        );
    }

    public static function createWithDefaultTagNames(): self
    {
        return new self(
            var: VarTagDefinition::NAME,
            varAliases: ['phpstan-var', 'psalm-var', 'phan-var'],
            return: ReturnTagDefinition::NAME,
            returnAliases: ['phpstan-return', 'psalm-return', 'phan-return'],
            param: ParamTagDefinition::NAME,
            paramAliases: ['phpstan-param', 'psalm-return', 'phan-return'],
        );
    }

    #[\Override]
    public function getTags(): iterable
    {
        return [
            $this->return => new ReturnTagDefinition(),
            $this->param => new ParamTagDefinition(),
            $this->var => new VarTagDefinition(),
        ];
    }

    #[\Override]
    public function getAliases(): iterable
    {
        foreach ($this->varAliases as $varAlias) {
            yield $varAlias => $this->var;
        }

        foreach ($this->paramAliases as $paramAlias) {
            yield $paramAlias => $this->param;
        }

        foreach ($this->returnAliases as $returnAlias) {
            yield $returnAlias => $this->return;
        }
    }
}
