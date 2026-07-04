<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Parser\TypeParser;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\ReferenceGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\TypeGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\UriGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\VariableGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\GenericTagDefinition;
use TypeLang\PhpDoc\Parser\Description\BalancedBraceAwareParser;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Tag\StringTagParser;
use TypeLang\PhpDoc\TagFactory;
use TypeLang\PhpDoc\TagFactoryInterface;

#[Group('type-lang/phpdoc')]
abstract class TestCase extends BaseTestCase
{
    private static ?TagFactoryInterface $cachedTagFactory = null;
    private static ?DescriptionParserInterface $cachedDescriptionParser = null;

    protected static function createTagFactory(): TagFactoryInterface
    {
        return self::$cachedTagFactory ??= self::buildTagFactory();
    }

    private static function buildTagFactory(): TagFactoryInterface
    {
        $typeParser = new TypeParser();

        $baseRules = [
            UriGrammarRule::NAME => new UriGrammarRule(),
            ReferenceGrammarRule::NAME => new ReferenceGrammarRule(),
            TypeGrammarRule::NAME => new TypeGrammarRule(typeParser: $typeParser),
            VariableGrammarRule::NAME => new VariableGrammarRule(),
        ];

        $tagFactory = null;

        $baseRules[DescriptionGrammarRule::NAME] = new \ReflectionClass(DescriptionGrammarRule::class)
            ->newLazyProxy(function () use (&$tagFactory): DescriptionGrammarRule {
                if ($tagFactory === null) {
                    return new DescriptionGrammarRule(new BalancedBraceAwareParser(
                        new StringTagParser(new TagFactory(
                            rules: [
                                UriGrammarRule::NAME => new UriGrammarRule(),
                                ReferenceGrammarRule::NAME => new ReferenceGrammarRule(),
                                TypeGrammarRule::NAME => new TypeGrammarRule(typeParser: new TypeParser()),
                                VariableGrammarRule::NAME => new VariableGrammarRule(),
                            ],
                            genericTagDefinition: new GenericTagDefinition(isInline: true),
                        )),
                    ));
                }

                return new DescriptionGrammarRule(
                    new BalancedBraceAwareParser(new StringTagParser($tagFactory)),
                );
            });

        $tagFactory = new TagFactory(
            rules: $baseRules,
            genericTagDefinition: new GenericTagDefinition(isInline: true),
        );
        return $tagFactory;
    }

    protected static function createDescriptionParser(): DescriptionParserInterface
    {
        if (self::$cachedDescriptionParser === null) {
            self::$cachedDescriptionParser = new BalancedBraceAwareParser(
                new StringTagParser(self::createTagFactory()),
            );
        }

        return self::$cachedDescriptionParser;
    }
}
