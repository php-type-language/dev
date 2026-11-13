<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

use TypeLang\PhpDoc\DocBlock\Combinator\FlowTypeCombinator;
use TypeLang\PhpDoc\DocBlock\Tag\AllowPrivateMutationTag\AllowPrivateMutationTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ApiTag\ApiTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\AssertIfFalseTag\AssertIfFalseTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\AssertIfTrueTag\AssertIfTrueTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\AssertTag\AssertTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\CheckTypeTag\PsalmCheckTypeExactTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\CheckTypeTag\PsalmCheckTypeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ConsistentConstructorTag\ConsistentConstructorTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ImmutableTag\ImmutableTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ImportTypeAliasTag\ImportTypeAliasTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\ExtendsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\ImplementsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\UseTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InternalTag\InternalTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\MethodTag\MethodTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamOutTag\ParamOutTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyReadTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyWriteTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmAssertUntaintedTag\PsalmAssertUntaintedTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmConsistentTemplatesTag\PsalmConsistentTemplatesTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmExternalMutationFreeTag\PsalmExternalMutationFreeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmFlowTag\PsalmFlowTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmIfThisIsTag\PsalmIfThisIsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmIgnoreFalsableReturnTag\PsalmIgnoreFalsableReturnTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmIgnoreNullableReturnTag\PsalmIgnoreNullableReturnTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmIgnoreVariableMethodTag\PsalmIgnoreVariableMethodTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmIgnoreVariablePropertyTag\PsalmIgnoreVariablePropertyTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmIgnoreVarTag\PsalmIgnoreVarTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmInheritorsTag\PsalmInheritorsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmMutationFreeTag\PsalmMutationFreeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmNoSealMethodsTag\PsalmNoSealMethodsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmNoSealPropertiesTag\PsalmNoSealPropertiesTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmOverrideMethodVisibilityTag\PsalmOverrideMethodVisibilityTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmOverridePropertyVisibilityTag\PsalmOverridePropertyVisibilityTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmScopeThisTag\PsalmScopeThisTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmStubOverrideTag\PsalmStubOverrideTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmTaintEscapeTag\PsalmTaintEscapeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmTaintSinkTag\PsalmTaintSinkTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmTaintSourceTag\PsalmTaintSourceTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmTaintSpecializeTag\PsalmTaintSpecializeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmTaintUnescapeTag\PsalmTaintUnescapeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmTraceTag\PsalmTraceTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PsalmVariadicTag\PsalmVariadicTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PureTag\PureTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReadonlyAllowPrivateMutationTag\ReadonlyAllowPrivateMutationTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReadonlyTag\ReadonlyTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\RequireInheritanceTag\RequireExtendsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\RequireInheritanceTag\RequireImplementsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SealMethodsTag\SealMethodsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SealPropertiesTag\SealPropertiesTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SelfOutTag\SelfOutTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SuppressTag\SuppressTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateContravariantTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateCovariantTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TypeAliasTag\TypeAliasTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\YieldTag\YieldTagDefinition;
use TypeLang\PhpDoc\DocBlock\TagDefinition\TagDefinitionInterface;
use TypeLang\PhpDoc\Parser\Grammar\CombinatorInterface;

/**
 * The Psalm platform: the `@psalm-*` tag family understood by Psalm.
 *
 * A prefixed tag that restates a common one gets the definition of that tag,
 * Psalm's own marker tags are contributed as their own flag definitions.
 *
 * @phpstan-import-type CombinatorType from CombinatorInterface
 */
final class PsalmPlatform extends Platform
{
    /**
     * @return iterable<non-empty-lowercase-string, TagDefinitionInterface>
     */
    public function getTags(): iterable
    {
        return [
            'psalm-allow-private-mutation' => new AllowPrivateMutationTagDefinition(),
            'psalm-api' => new ApiTagDefinition(),
            'psalm-assert' => new AssertTagDefinition(),
            'psalm-assert-if-true' => new AssertIfTrueTagDefinition(),
            'psalm-assert-if-false' => new AssertIfFalseTagDefinition(),
            'psalm-consistent-constructor' => new ConsistentConstructorTagDefinition(),
            'psalm-extends' => new ExtendsTagDefinition(),
            'psalm-immutable' => new ImmutableTagDefinition(),
            'psalm-implements' => new ImplementsTagDefinition(),
            'psalm-import-type' => new ImportTypeAliasTagDefinition(),
            'psalm-internal' => new InternalTagDefinition(),
            'psalm-method' => new MethodTagDefinition(),
            'psalm-param' => new ParamTagDefinition(),
            'psalm-param-out' => new ParamOutTagDefinition(),
            'psalm-property' => new PropertyTagDefinition(),
            'psalm-property-read' => new PropertyReadTagDefinition(),
            'psalm-property-write' => new PropertyWriteTagDefinition(),
            'psalm-pure' => new PureTagDefinition(),
            'psalm-readonly' => new ReadonlyTagDefinition(),
            'psalm-readonly-allow-private-mutation' => new ReadonlyAllowPrivateMutationTagDefinition(),
            'psalm-require-extends' => new RequireExtendsTagDefinition(),
            'psalm-require-implements' => new RequireImplementsTagDefinition(),
            'psalm-return' => new ReturnTagDefinition(),
            'psalm-seal-methods' => new SealMethodsTagDefinition(),
            'psalm-seal-properties' => new SealPropertiesTagDefinition(),
            'psalm-self-out' => new SelfOutTagDefinition(),
            'psalm-suppress' => new SuppressTagDefinition(),
            'psalm-template' => new TemplateTagDefinition(),
            'psalm-template-contravariant' => new TemplateContravariantTagDefinition(),
            'psalm-template-covariant' => new TemplateCovariantTagDefinition(),
            'psalm-type' => new TypeAliasTagDefinition(),
            'psalm-use' => new UseTagDefinition(),
            'psalm-var' => new VarTagDefinition(),
            'psalm-yield' => new YieldTagDefinition(),
            PsalmCheckTypeTagDefinition::NAME => new PsalmCheckTypeTagDefinition(),
            PsalmCheckTypeExactTagDefinition::NAME => new PsalmCheckTypeExactTagDefinition(),
            PsalmAssertUntaintedTagDefinition::NAME => new PsalmAssertUntaintedTagDefinition(),
            PsalmConsistentTemplatesTagDefinition::NAME => new PsalmConsistentTemplatesTagDefinition(),
            PsalmIfThisIsTagDefinition::NAME => new PsalmIfThisIsTagDefinition(),
            PsalmInheritorsTagDefinition::NAME => new PsalmInheritorsTagDefinition(),
            PsalmScopeThisTagDefinition::NAME => new PsalmScopeThisTagDefinition(),
            PsalmExternalMutationFreeTagDefinition::NAME => new PsalmExternalMutationFreeTagDefinition(),
            PsalmFlowTagDefinition::NAME => new PsalmFlowTagDefinition(),
            PsalmMutationFreeTagDefinition::NAME => new PsalmMutationFreeTagDefinition(),
            PsalmIgnoreFalsableReturnTagDefinition::NAME => new PsalmIgnoreFalsableReturnTagDefinition(),
            PsalmIgnoreNullableReturnTagDefinition::NAME => new PsalmIgnoreNullableReturnTagDefinition(),
            PsalmIgnoreVarTagDefinition::NAME => new PsalmIgnoreVarTagDefinition(),
            PsalmIgnoreVariableMethodTagDefinition::NAME => new PsalmIgnoreVariableMethodTagDefinition(),
            PsalmIgnoreVariablePropertyTagDefinition::NAME => new PsalmIgnoreVariablePropertyTagDefinition(),
            PsalmNoSealMethodsTagDefinition::NAME => new PsalmNoSealMethodsTagDefinition(),
            PsalmNoSealPropertiesTagDefinition::NAME => new PsalmNoSealPropertiesTagDefinition(),
            PsalmOverrideMethodVisibilityTagDefinition::NAME => new PsalmOverrideMethodVisibilityTagDefinition(),
            PsalmOverridePropertyVisibilityTagDefinition::NAME => new PsalmOverridePropertyVisibilityTagDefinition(),
            PsalmStubOverrideTagDefinition::NAME => new PsalmStubOverrideTagDefinition(),
            PsalmTaintEscapeTagDefinition::NAME => new PsalmTaintEscapeTagDefinition(),
            PsalmTaintSinkTagDefinition::NAME => new PsalmTaintSinkTagDefinition(),
            PsalmTaintSourceTagDefinition::NAME => new PsalmTaintSourceTagDefinition(),
            PsalmTaintSpecializeTagDefinition::NAME => new PsalmTaintSpecializeTagDefinition(),
            PsalmTaintUnescapeTagDefinition::NAME => new PsalmTaintUnescapeTagDefinition(),
            PsalmTraceTagDefinition::NAME => new PsalmTraceTagDefinition(),
            PsalmVariadicTagDefinition::NAME => new PsalmVariadicTagDefinition(),
        ];
    }

    /**
     * @return iterable<non-empty-lowercase-string, non-empty-lowercase-string>
     */
    public function getAliases(): iterable
    {
        return [
            'psalm-this-out' => 'psalm-self-out',
        ];
    }

    /**
     * @return iterable<non-empty-string, CombinatorType>
     */
    public function getCombinators(): iterable
    {
        return [
            FlowTypeCombinator::NAME => new FlowTypeCombinator(),
        ];
    }
}
