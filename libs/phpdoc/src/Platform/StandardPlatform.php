<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\AbstractTag\AbstractTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ApiTag\ApiTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\CategoryTag\CategoryTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\CopyrightTag\CopyrightTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\DocBlock\Tag\FinalTag\FinalTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\IgnoreTag\IgnoreTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\InheritDocTag\InheritDocTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\LicenseTag\LicenseTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\LinkTag\LinkTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\MethodTag\MethodTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\NoNamedArgumentsTag\NoNamedArgumentsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\OverrideTag\OverrideTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PackageTag\PackageTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PackageTag\SubPackageTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyReadTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyWriteTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\SeeTag\SeeTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag\TemplateExtendsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag\TemplateImplementsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateContravariantTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateCovariantTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag\ThrowsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTagFactory;

final class StandardPlatform extends Platform
{
    public function getName(): string
    {
        return 'Standard';
    }

    protected function load(TypesParserInterface $types): iterable
    {
        yield 'abstract' => new AbstractTagFactory();
        yield 'api' => new ApiTagFactory();
        yield 'category' => new CategoryTagFactory();
        yield 'copyright' => new CopyrightTagFactory();
        yield 'extends' => new TemplateExtendsTagFactory($types);
        yield 'final' => new FinalTagFactory();
        yield 'implements' => new TemplateImplementsTagFactory($types);
        yield 'inheritdoc' => new InheritDocTagFactory();
        yield 'ignore' => new IgnoreTagFactory();
        yield 'license' => new LicenseTagFactory();
        yield 'link' => new LinkTagFactory();
        yield 'method' => new MethodTagFactory($types);
        yield 'no-named-arguments' => new NoNamedArgumentsTagFactory();
        yield 'package' => new PackageTagFactory($types);
        yield 'override' => new OverrideTagFactory();
        yield 'param' => new ParamTagFactory($types);
        yield 'property' => new PropertyTagFactory($types);
        yield 'property-read' => new PropertyReadTagFactory($types);
        yield 'property-write' => new PropertyWriteTagFactory($types);
        yield 'return' => new ReturnTagFactory($types);
        yield 'see' => new SeeTagFactory($types);
        yield 'subpackage' => new SubPackageTagFactory($types);
        yield 'template' => new TemplateTagFactory($types);
        yield 'template-contravariant' => new TemplateContravariantTagFactory($types);
        yield 'template-covariant' => new TemplateCovariantTagFactory($types);
        yield 'use' => new TemplateExtendsTagFactory($types);
        yield 'throws' => new ThrowsTagFactory($types);
        yield 'var' => new VarTagFactory($types);

        yield from $this->loadAliases($types);
    }

    /**
     * @return iterable<non-empty-lowercase-string|iterable<mixed, non-empty-lowercase-string>, TagFactoryInterface>
     */
    protected function loadAliases(TypesParserInterface $types): iterable
    {
        yield 'returns' => new ReturnTagFactory($types);
        yield ['template-extends', 'inherits'] => new TemplateExtendsTagFactory($types);
        yield 'template-implements' => new TemplateImplementsTagFactory($types);
        yield 'template-use' => new TemplateExtendsTagFactory($types);
        yield 'throw' => new ThrowsTagFactory($types);
    }
}
