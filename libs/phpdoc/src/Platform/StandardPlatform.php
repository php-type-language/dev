<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

use TypeLang\PhpDoc\DocBlock\Tag\AbstractTag\AbstractTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ApiTag\ApiTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\DeprecatedTag\DeprecatedTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\FinalTag\FinalTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ImmutableTag\ImmutableTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\ExtendsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\ImplementsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InheritanceTag\UseTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\InternalTag\InternalTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\LinkTag\LinkTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\MethodTag\MethodTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\MixinTag\MixinTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\NoNamedArgumentsTag\NoNamedArgumentsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\NotDeprecatedTag\NotDeprecatedTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\OverrideTag\OverrideTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamClosureThisTag\ParamClosureThisTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamInvokedCallableTag\ParamImmediatelyInvokedCallableTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamInvokedCallableTag\ParamLaterInvokedCallableTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamOutTag\ParamOutTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyReadTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyWriteTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\PureUnlessCallableIsImpureTag\PureUnlessCallableIsImpureTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReadonlyTag\ReadonlyTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\RequireInheritanceTag\RequireExtendsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\RequireInheritanceTag\RequireImplementsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SealMethodsTag\SealMethodsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SealPropertiesTag\SealPropertiesTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SeeTag\SeeTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\SuppressTag\SuppressTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateContravariantTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateCovariantTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag\ThrowsTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\UnusedParamTag\UnusedParamTagDefinition;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTagDefinition;
use TypeLang\PhpDoc\DocBlock\TagDefinition\TagDefinitionInterface;

/**
 * The standard platform: the common tags, without a tool prefix, understood by
 * most of the documentation and static analysis tools.
 */
final class StandardPlatform extends Platform
{
    /**
     * @return iterable<non-empty-lowercase-string, TagDefinitionInterface>
     */
    public function getTags(): iterable
    {
        return [
            LinkTagDefinition::NAME => new LinkTagDefinition(),
            SeeTagDefinition::NAME => new SeeTagDefinition(),
            ReturnTagDefinition::NAME => new ReturnTagDefinition(),
            ThrowsTagDefinition::NAME => new ThrowsTagDefinition(),
            MixinTagDefinition::NAME => new MixinTagDefinition(),
            ExtendsTagDefinition::NAME => new ExtendsTagDefinition(),
            ImplementsTagDefinition::NAME => new ImplementsTagDefinition(),
            UseTagDefinition::NAME => new UseTagDefinition(),
            RequireExtendsTagDefinition::NAME => new RequireExtendsTagDefinition(),
            RequireImplementsTagDefinition::NAME => new RequireImplementsTagDefinition(),
            ParamTagDefinition::NAME => new ParamTagDefinition(),
            ParamOutTagDefinition::NAME => new ParamOutTagDefinition(),
            ParamClosureThisTagDefinition::NAME => new ParamClosureThisTagDefinition(),
            PropertyTagDefinition::NAME => new PropertyTagDefinition(),
            PropertyReadTagDefinition::NAME => new PropertyReadTagDefinition(),
            PropertyWriteTagDefinition::NAME => new PropertyWriteTagDefinition(),
            ParamImmediatelyInvokedCallableTagDefinition::NAME => new ParamImmediatelyInvokedCallableTagDefinition(),
            ParamLaterInvokedCallableTagDefinition::NAME => new ParamLaterInvokedCallableTagDefinition(),
            UnusedParamTagDefinition::NAME => new UnusedParamTagDefinition(),
            AbstractTagDefinition::NAME => new AbstractTagDefinition(),
            ApiTagDefinition::NAME => new ApiTagDefinition(),
            FinalTagDefinition::NAME => new FinalTagDefinition(),
            InternalTagDefinition::NAME => new InternalTagDefinition(),
            NoNamedArgumentsTagDefinition::NAME => new NoNamedArgumentsTagDefinition(),
            OverrideTagDefinition::NAME => new OverrideTagDefinition(),
            ReadonlyTagDefinition::NAME => new ReadonlyTagDefinition(),
            ImmutableTagDefinition::NAME => new ImmutableTagDefinition(),
            SealMethodsTagDefinition::NAME => new SealMethodsTagDefinition(),
            SealPropertiesTagDefinition::NAME => new SealPropertiesTagDefinition(),
            PureUnlessCallableIsImpureTagDefinition::NAME => new PureUnlessCallableIsImpureTagDefinition(),
            TemplateTagDefinition::NAME => new TemplateTagDefinition(),
            TemplateCovariantTagDefinition::NAME => new TemplateCovariantTagDefinition(),
            TemplateContravariantTagDefinition::NAME => new TemplateContravariantTagDefinition(),
            DeprecatedTagDefinition::NAME => new DeprecatedTagDefinition(),
            NotDeprecatedTagDefinition::NAME => new NotDeprecatedTagDefinition(),
            VarTagDefinition::NAME => new VarTagDefinition(),
            SuppressTagDefinition::NAME => new SuppressTagDefinition(),
            MethodTagDefinition::NAME => new MethodTagDefinition(),
        ];
    }

    /**
     * @return iterable<non-empty-lowercase-string, non-empty-lowercase-string>
     */
    public function getAliases(): iterable
    {
        return [
            'inherits' => ExtendsTagDefinition::NAME,
            'template-extends' => ExtendsTagDefinition::NAME,
            'template-implements' => ImplementsTagDefinition::NAME,
            'template-use' => UseTagDefinition::NAME,
            'template-invariant' => TemplateTagDefinition::NAME,
            // A fairly common typos in code
            'returns' => ReturnTagDefinition::NAME,
            'throw' => ThrowsTagDefinition::NAME,
        ];
    }
}
