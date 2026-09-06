<?php

declare(strict_types=1);

namespace TypeLang\Type\Template;

use TypeLang\Type\Identifier;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

final class TemplateArgumentNode extends Node
{
    public ?Identifier $hint;

    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public TypeNode $value,
        ?Identifier $hint = null,
        int $offset = 0,
    ) {
        $this->hint = $hint;

        parent::__construct($offset);
    }
}
