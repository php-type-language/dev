<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\SubParser;

use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

/**
 * Cuts the text of a mask token into the words and the stars.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-implements SubParserInterface<ConstMaskTypeNode>
 */
final class ConstMaskParser implements SubParserInterface
{
    public function __construct(
        private readonly ClassConstMaskParser $parser,
    ) {}

    /**
     * Reads a mask of global constants, which may stand behind a namespace:
     * `PHP_*`, `\PHP_*`, `Foo\BAR_*`, `Path\To\*`, `\*`.
     *
     * @param non-empty-string $raw
     * @param int<0, max> $offset
     */
    public function parse(string $raw, int $offset = 0): ConstMaskTypeNode
    {
        $separator = \strrpos($raw, '\\');

        if ($separator === false) {
            return new ConstMaskTypeNode(
                mask: $this->parser->parse($raw, $offset),
                namespace: null,
                isFullyQualified: false,
                offset: $offset,
            );
        }

        $mask = \substr($raw, $separator + 1);
        \assert($mask !== '');

        // the root namespace has no name of its own, the flag tells about it
        $namespace = null;

        if ($separator > 0) {
            $namespace = new Name(\substr($raw, 0, $separator), $offset);
        }

        /** @var int<0, max> $maskOffset */
        $maskOffset = $offset + $separator + 1;

        return new ConstMaskTypeNode(
            mask: $this->parser->parse($mask, $maskOffset),
            namespace: $namespace,
            isFullyQualified: $raw[0] === '\\',
            offset: $offset,
        );
    }
}
