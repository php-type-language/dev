<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Platform;

use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\MethodTag\MethodTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ParamTag\ParamTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyReadTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\PropertyTag\PropertyWriteTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ReturnTag\ReturnTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag\TemplateExtendsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateExtendsTag\TemplateImplementsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateContravariantTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateCovariantTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\TemplateTag\TemplateTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag\ThrowsTagFactory;
use TypeLang\PhpDoc\DocBlock\Tag\VarTag\VarTagFactory;

final class PHPStanPlatform extends Platform
{
    public function getName(): string
    {
        return 'PHPStan';
    }

    protected function load(TypesParserInterface $types): iterable
    {
        yield 'phpstan-method' => new MethodTagFactory($types);
        yield 'phpstan-param' => new ParamTagFactory($types);
        yield 'phpstan-property' => new PropertyTagFactory($types);
        yield 'phpstan-property-read' => new PropertyReadTagFactory($types);
        yield 'phpstan-property-write' => new PropertyWriteTagFactory($types);
        yield 'phpstan-return' => new ReturnTagFactory($types);
        yield 'phpstan-template' => new TemplateTagFactory($types);
        yield 'phpstan-implements' => new TemplateImplementsTagFactory($types);
        yield 'phpstan-extends' => new TemplateExtendsTagFactory($types);
        yield 'phpstan-use' => new TemplateExtendsTagFactory($types);
        yield 'phpstan-template-covariant' => new TemplateCovariantTagFactory($types);
        yield 'phpstan-template-contravariant' => new TemplateContravariantTagFactory($types);
        yield 'phpstan-throws' => new ThrowsTagFactory($types);
        yield 'phpstan-var' => new VarTagFactory($types);
    }
}
