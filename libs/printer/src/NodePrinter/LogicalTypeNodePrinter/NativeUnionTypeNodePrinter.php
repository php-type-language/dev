<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;

use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;

/**
 * Prints a union as the native one: a "true" with a "false" are a "bool", and
 * a "mixed" swallows the rest.
 *
 * @template-extends LogicalTypeNodePrinter<UnionTypeNode>
 */
final class NativeUnionTypeNodePrinter extends LogicalTypeNodePrinter
{
    protected function getDelimiter(PrinterOptions $options): string
    {
        return '|';
    }

    protected function normalize(array $types): array
    {
        if (\in_array('mixed', $types, true)) {
            return ['mixed'];
        }

        $containsBool = (\in_array('true', $types, true) || \in_array('\true', $types, true))
            && (\in_array('false', $types, true) || \in_array('\false', $types, true));

        if (!$containsBool) {
            return $types;
        }

        $filtered = \array_filter($types, static fn(string $type): bool
            => !\in_array($type, ['true', 'false', '\true', '\false'], true));
        $filtered[] = 'bool';

        return \array_values($filtered);
    }
}
