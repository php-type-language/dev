<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * @template-covariant TTag of TagInterface = TagInterface
 */
interface TagFactoryInterface
{
    /**
     * @param non-empty-string $name
     * @return TTag
     */
    public function create(
        string $name,
        string $suffix,
        DescriptionParserInterface $descriptions,
    ): TagInterface;
}
