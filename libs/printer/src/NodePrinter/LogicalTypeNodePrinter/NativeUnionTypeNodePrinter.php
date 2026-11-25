<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;

/**
 * Prints a union as the native one: a `true` with a `false` are a `bool`, and
 * a `mixed` swallows the rest.
 *
 * @template-extends LogicalTypeNodePrinter<UnionTypeNode>
 */
final class NativeUnionTypeNodePrinter extends LogicalTypeNodePrinter
{
    protected function getDelimiter(PrinterOptions $options): string
    {
        return '|';
    }

    /**
     * A nullable member is its type with a `null`, a conditional one is its
     * branches, a name of an alias is the types of the alias.
     *
     * @throws PrinterExceptionInterface in case of a child node cannot be
     *         printed
     */
    protected function expand(TypeNode $type, PrinterContext $context): array
    {
        if ($type instanceof NullableTypeNode) {
            return [...$this->expand($type->type, $context), 'null'];
        }

        if ($type instanceof ConditionalTypeNode) {
            return [...$this->expand($type->then, $context), ...$this->expand($type->else, $context)];
        }

        if ($type instanceof UnionTypeNode) {
            $result = [];

            foreach ($type->children as $child) {
                \array_push($result, ...$this->expand($child, $context));
            }

            return $result;
        }

        if ($type instanceof NamedTypeNode) {
            return \explode('|', $context->isolate(static fn(): string => $context->print($type)));
        }

        return parent::expand($type, $context);
    }

    protected function normalize(array $types): array
    {
        if (\in_array('mixed', $types, true)) {
            return ['mixed'];
        }

        $containsBool = (\in_array('true', $types, true) || \in_array('\true', $types, true))
            && (\in_array('false', $types, true) || \in_array('\false', $types, true));

        if ($containsBool) {
            $types = \array_filter($types, static fn(string $type): bool
                => !\in_array($type, ['true', 'false', '\true', '\false'], true));
            $types[] = 'bool';
        }

        return $this->moveNullToEnd(\array_values($types));
    }

    /**
     * @param list<string> $types
     * @return list<string>
     */
    private function moveNullToEnd(array $types): array
    {
        $filtered = \array_values(\array_filter($types, static fn(string $type): bool
            => !\in_array($type, ['null', '\null'], true)));

        if (\count($filtered) === \count($types)) {
            return $types;
        }

        $filtered[] = 'null';

        return $filtered;
    }
}
