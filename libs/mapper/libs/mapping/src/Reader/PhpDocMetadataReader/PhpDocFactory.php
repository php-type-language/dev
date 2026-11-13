<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;

use TypeLang\PhpDoc\DocBlockParser;

/**
 * Builds the docblock parser and the property type providers of the
 * configured tag names.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class PhpDocFactory
{
    private function __construct(
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $varTags,
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $paramTags,
        /**
         * @var list<non-empty-lowercase-string>
         */
        private readonly array $returnTags,
    ) {}

    /**
     * @param list<non-empty-string> $varTags names of `@var` tags
     * @param list<non-empty-string> $paramTags names of `@param` tags
     * @param list<non-empty-string> $returnTags names of `@return` tags
     */
    public static function fromTagNames(array $varTags, array $paramTags, array $returnTags): self
    {
        return new self(
            varTags: \array_map(\strtolower(...), $varTags),
            paramTags: \array_map(\strtolower(...), $paramTags),
            returnTags: \array_map(\strtolower(...), $returnTags),
        );
    }

    /**
     * Returns the parser reading each configured name as its tag.
     */
    public function createParser(): DocBlockParser
    {
        return DocBlockParser::createDefault([
            new TagAliasPlatform([
                ...\array_fill_keys($this->varTags, 'var'),
                ...\array_fill_keys($this->paramTags, 'param'),
                ...\array_fill_keys($this->returnTags, 'return'),
            ]),
        ]);
    }

    /**
     * Returns the providers by priority: the hooks on PHP 8.4 and later,
     * `@var` of the property, `@param` of the constructor.
     *
     * @return non-empty-list<PropertyTypeProviderInterface>
     */
    public function createProviders(): array
    {
        $providers = [];

        if (\PHP_VERSION_ID >= 80400) {
            $providers[] = new HookTypeProvider($this->paramTags, $this->returnTags);
        }

        $providers[] = new VarTagTypeProvider($this->varTags);
        $providers[] = new PromotedParamTypeProvider($this->paramTags);

        return $providers;
    }
}
