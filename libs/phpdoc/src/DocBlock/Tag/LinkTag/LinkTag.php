<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\LinkTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Reference\UriReference;
use TypeLang\PhpDoc\DocBlock\Tag\Tag;

final class LinkTag extends Tag
{
    public function __construct(
        string $name,
        public readonly UriReference $uri,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }

    public function __toString(): string
    {
        $result = \sprintf('@%s %s', $this->name, $this->uri);

        if ($this->description !== null) {
            $result .= ' ' . $this->description;
        }

        return $result;
    }
}
