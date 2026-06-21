<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\AbstractTag\AbstractTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\MethodTag\MethodTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\OverrideTag\OverrideTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyReadTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyWriteTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag\TemplateExtendsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTagFactory;

final class PhanPlatform extends Platform
{
    public function getName(): string
    {
        return 'Phan';
    }

    protected function load(TypesParserInterface $types): iterable
    {
        yield 'phan-abstract' => new AbstractTagFactory();
        yield 'phan-method' => new MethodTagFactory($types);
        yield 'phan-override' => new OverrideTagFactory();
        yield 'phan-param' => new ParamTagFactory($types);
        yield 'phan-property' => new PropertyTagFactory($types);
        yield 'phan-property-read' => new PropertyReadTagFactory($types);
        yield 'phan-property-write' => new PropertyWriteTagFactory($types);
        yield 'phan-return' => new ReturnTagFactory($types);
        yield 'phan-template' => new TemplateTagFactory($types);
        yield ['phan-inherits', 'phan-extends'] => new TemplateExtendsTagFactory($types);
        yield 'phan-var' => new VarTagFactory($types);
    }
}
