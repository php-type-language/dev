<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Parser\TypeResolver\PhpUseStatementsTransformer;
use TypeLang\Type\TypeNode;

final readonly class TypeResolver
{
    public function __construct(
        /**
         * @var array<array-key, non-empty-string>
         */
        private array $imports = [],
    ) {}

    /**
     * @api
     * @param non-empty-string $name
     */
    public function withTypeImport(string $name): self
    {
        return new self([...$this->imports, $name]);
    }

    /**
     * @api
     * @param non-empty-string $name
     * @param non-empty-string $alias
     */
    public function withTypeImportAs(string $name, string $alias): self
    {
        return new self([...$this->imports, $alias => $name]);
    }

    private function createTraverser(): TraverserInterface
    {
        $transformer = new PhpUseStatementsTransformer($this->imports);

        return new Traverser([
            new TypeMapVisitor($transformer(...)),
        ]);
    }

    public function resolve(TypeNode $type): TypeNode
    {
        $traverser = $this->createTraverser();
        $traverser->traverse([$type]);

        return $type;
    }
}
