<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

final readonly class TagFactory implements TagFactoryInterface
{
    public function create(string $name, string $suffix, DescriptionParserInterface $descriptions): TagInterface
    {
        return new Tag($name, $descriptions->tryParse($suffix));
    }
}
