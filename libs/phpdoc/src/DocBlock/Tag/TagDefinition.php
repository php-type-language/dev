<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

abstract class TagDefinition implements TagDefinitionInterface
{
    public function __toString(): string
    {
        return \sprintf('"@%s" %s', $this->name, $this->rule);
    }
}
