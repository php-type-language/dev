<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\Exception\PhpDocExceptionInterface;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;

/**
 * @template-covariant TTag of TagInterface = TagInterface
 *
 * @template-extends \Traversable<non-empty-string, TagDefinitionInterface>
 */
interface TagFactoryInterface extends \Traversable, \Countable
{
    /**
     * @param non-empty-string $name
     * @return TTag
     * @throws PhpDocExceptionInterface
     */
    public function create(
        string $name,
        string $suffix,
        DescriptionParserInterface $descriptions,
    ): TagInterface;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
