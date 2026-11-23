<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\Option;

use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Options of the properties and of the classes the attribute and the config
 * readers read, in the order they are read.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class Options
{
    /**
     * @param list<PropertyOptionInterface> $properties
     * @param list<ClassOptionInterface> $classes
     */
    public function __construct(
        public readonly array $properties,
        public readonly array $classes,
    ) {}

    /**
     * @param TypeParserInterface<TypeNode, string> $parser parser of the types
     */
    public static function createDefault(TypeParserInterface $parser): self
    {
        $types = new TypeOption($parser);
        $messages = new MessageOption();
        $aliases = new AliasOption();

        return new self(
            properties: [
                new NameOption(),
                $aliases,
                $types,
                new DefaultOption(),
                new SkipOption(),
                $messages,
            ],
            classes: [
                $types,
                new DiscriminatorOption($parser),
                $messages,
                new NormalizeAsArrayOption(),
                new NamingStrategyOption(),
                $aliases,
            ],
        );
    }
}
