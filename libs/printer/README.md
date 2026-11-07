<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

<p align="center">
    <a href="https://packagist.org/packages/type-lang/printer"><img src="https://poser.pugx.org/type-lang/printer/require/php?style=for-the-badge" alt="PHP 8.4+"></a>
    <a href="https://packagist.org/packages/type-lang/printer"><img src="https://poser.pugx.org/type-lang/printer/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/printer"><img src="https://poser.pugx.org/type-lang/printer/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/printer/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/printer/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/printer/actions"><img src="https://github.com/php-type-language/printer/workflows/tests/badge.svg"></a>
</p>

---

## About

The reference printer for **TypeLang**. It renders `TypeLang\Type\*` AST nodes
back into their string representation.

Two printers are provided:

- `NativeTypePrinter` — outputs a valid, native PHP type declaration.
- `PrettyTypePrinter` — outputs the full PHPStan/Psalm-style type, formatted
  across multiple lines.

Full documentation is available at [typelang.dev](https://typelang.dev).

## Installation

Install the package via [Composer](https://getcomposer.org):

```sh
composer require type-lang/printer
```

**Requirements:** 
- PHP 8.1+

## Usage

Parse a type into an AST (using [`type-lang/parser`](https://packagist.org/packages/type-lang/parser)),
then print it back with either printer:

```php
$parser = new TypeLang\Parser\TypeParser();

$type = $parser->parse(<<<'PHP'
    array{
        field1: (callable(Example, int): mixed),
        field2: list<Some>,
        ...
    }
    PHP);

echo (new TypeLang\Printer\NativeTypePrinter())->print($type);
// array

echo (new TypeLang\Printer\PrettyTypePrinter())->print($type);
// array{
//     field1: callable(Example, int): mixed,
//     field2: list<Some>,
//     ...
// }
```


### Options

The formatting is set with the `PrinterOptions`:

```php
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\CommaSpacing;
use TypeLang\Printer\PrinterOptions\ListStyle;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use TypeLang\Printer\PrinterOptions\UnionSpacing;

$printer = new PrettyTypePrinter(new PrinterOptions(
    unionSpacing: UnionSpacing::Spaced,        // int | string
    maxInlineTemplateItems: 2,             // array<K, V> is written one argument per line after 2 of them
    commaSpacing: CommaSpacing::Compact,       // array<K,V>
    nullableStyle: NullableStyle::Union,   // int|null instead of ?int
    listStyle: ListStyle::Generic,         // array<int> instead of int[]
));
```

### Printing a node in your own way

Every kind of node is printed by its own `NodePrinterInterface`, and a printer
looks them up in a registry. To change how a node is printed, replace its
printer instead of extending the printer:

```php
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\TypePrinter;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;

/**
 * @template-implements NodePrinterInterface<NamedTypeNode>
 */
final class ShoutingNamePrinter implements NodePrinterInterface
{
    public function printNode(Node $node, PrinterContext $context): string
    {
        return \strtoupper($node->name->toString());
    }
}

$printer = new TypePrinter(
    PrettyTypePrinter::createRegistry()->with(NamedTypeNode::class, new ShoutingNamePrinter()),
);
```

A node without its own printer is printed by the printer of the closest parent
class. The `PrinterContext` prints the children of a node (`$context->print()`)
and carries the options.
