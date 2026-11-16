<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\Internal;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\ExpressionSkipCondition;

/**
 * Creates the expression conditions of the attributes and of the config,
 * the expressions are parsed once, when the metadata is read.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class SkipConditionFactory
{
    public function __construct(
        /**
         * Parser of the expressions, created on the first expression in
         * case of {@see null}
         */
        private ?ExpressionLanguage $language = null,
    ) {}

    /**
     * @param non-empty-string $variable name of the object in the expression
     * @throws InvalidValueException in case of the expression cannot be
     *         parsed
     * @throws MissingDependencyException in case of the
     *         `symfony/expression-language` package is not installed
     */
    public function createExpression(
        string $expression,
        string $variable,
        SourceContext $context,
    ): ExpressionSkipCondition {
        $this->language ??= self::createLanguage();

        try {
            $parsed = $this->language->parse($expression, [$variable]);
        } catch (SyntaxError $e) {
            throw InvalidValueException::becauseExpressionIsInvalid(
                expression: $expression,
                target: $context->target,
                source: $context->source,
                previous: $e,
            );
        }

        return new ExpressionSkipCondition($parsed, $variable);
    }

    /**
     * @throws MissingDependencyException in case of the
     *         `symfony/expression-language` package is not installed
     */
    private static function createLanguage(): ExpressionLanguage
    {
        if (!\class_exists(ExpressionLanguage::class)) {
            throw MissingDependencyException::becausePackageIsNotInstalled(
                package: 'symfony/expression-language',
                purpose: 'Skipping a property by an expression',
            );
        }

        return new ExpressionLanguage();
    }
}
