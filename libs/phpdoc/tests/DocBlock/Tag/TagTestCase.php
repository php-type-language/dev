<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use TypeLang\PhpDoc\DocBlock\TagDefinition\TagDefinitionInterface;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\DocBlockParserInterface;
use TypeLang\PhpDoc\Parser\Grammar\CombinatorInterface;
use TypeLang\PhpDoc\Parser\TagFactory;
use TypeLang\PhpDoc\Parser\TagRegistry;
use TypeLang\PhpDoc\Platform\PlatformInterface;
use TypeLang\PhpDoc\Platform\StandardPlatform;
use TypeLang\PhpDoc\TagFactoryInterface;
use TypeLang\PhpDoc\TagRegistryInterface;
use TypeLang\PhpDoc\Tests\TestCase;

/**
 * @phpstan-import-type CombinatorType from CombinatorInterface
 */
abstract class TagTestCase extends TestCase
{
    /**
     * @param array<non-empty-string, TagDefinitionInterface> $tags
     * @param array<non-empty-string, non-empty-string> $aliases
     */
    protected function createTagRegistry(
        array $tags = [],
        array $aliases = [],
    ): TagRegistryInterface {
        return new TagRegistry($tags, $aliases);
    }

    /**
     * @param array<non-empty-string, TagDefinitionInterface> $tags
     * @param array<non-empty-string, CombinatorType> $combinators
     * @param array<non-empty-string, non-empty-string> $aliases
     */
    protected function createFactory(
        array $tags = [],
        array $combinators = [],
        array $aliases = [],
    ): TagFactoryInterface {
        $registry = $this->createTagRegistry($tags, $aliases);

        return new TagFactory($registry, $combinators);
    }

    /**
     * @param array<non-empty-string, TagDefinitionInterface> $tags
     * @param array<non-empty-string, CombinatorType> $combinators
     * @param array<non-empty-string, non-empty-string> $aliases
     */
    protected function createPlatform(
        array $tags = [],
        array $combinators = [],
        array $aliases = [],
    ): PlatformInterface {
        return new readonly class('testing', $tags, $aliases, $combinators) implements PlatformInterface {
            public function __construct(
                /** @var non-empty-string */
                public string $name,
                /** @var iterable<non-empty-string, TagDefinitionInterface> */
                public iterable $tags,
                /** @var iterable<non-empty-string, non-empty-string> */
                public iterable $aliases,
                /** @var iterable<non-empty-string, CombinatorType> */
                public iterable $combinators,
            ) {}
        };
    }

    /**
     * @param array<non-empty-string, TagDefinitionInterface> $tags
     * @param array<non-empty-string, CombinatorType> $combinators
     * @param array<non-empty-string, non-empty-string> $aliases
     */
    protected function createDocBlockParser(
        array $tags = [],
        array $combinators = [],
        array $aliases = [],
    ): DocBlockParserInterface {
        return new DocBlockParser([
            $this->createPlatform($tags, $combinators, $aliases),
        ]);
    }
}
