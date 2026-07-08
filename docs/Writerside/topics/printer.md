# Type Printer Component

<primary-label ref="printer-component"/>
<link-summary>
Turns a `TypeLang\Type\*` AST back into a type string, either as a
high-fidelity TypeLang rendering or as a PHP-compatible one.
</link-summary>
<show-structure for="chapter" depth="2"/>

The printer component is the inverse of the [parser](parser.md): where the
parser turns a type string into an <tooltip term="AST">AST</tooltip>, the
printer turns that AST back into a string. It walks a
`TypeLang\Type\TypeNode` and renders every node — a union, a shape, a
conditional type — into readable text.

It ships with two printers that render the very same AST differently:

<deflist>
    <def title="TypeLang\Printer\PrettyTypePrinter">
        Renders the type as faithfully as possible, preserving every detail
        the AST carries — shapes, generics, conditional types and all. See
        <a href="pretty-printer.md">Pretty Printer</a>.
    </def>
    <def title="TypeLang\Printer\NativeTypePrinter">
        Renders the closest type PHP itself would accept, collapsing anything
        without a native equivalent down to a supported approximation. See
        <a href="native-printer.md">Native Printer</a>.
    </def>
</deflist>

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/printer</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.4`
* `ext-mbstring` <sup>optional</sup>

## Quick Start

Every printer implements `TypeLang\Printer\TypePrinterInterface`, a single
`print()` method that takes a `TypeNode` and returns its string form. The AST
usually comes straight from the [parser](parser.md), but any hand-built or
rewritten `TypeLang\Type\*` node graph prints just as well.

<tabs>
<tab title="PrettyTypePrinter">

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Printer\PrettyTypePrinter;

$type = new TypeParser()->parse('non-empty-list<positive-int>|null');

echo new PrettyTypePrinter()->print($type);
```

> Renders the type exactly as written, only normalizing whitespace.
> ```
> non-empty-list<positive-int> | null
> ```

</tab>
<tab title="NativeTypePrinter">

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Printer\NativeTypePrinter;

$type = new TypeParser()->parse('non-empty-list<positive-int>|null');

echo new NativeTypePrinter()->print($type);
```

> Collapses everything down to a type PHP would accept: `non-empty-list<…>`
> becomes `array`.
> ```
> array|null
> ```

</tab>
</tabs>

<tip>
A printer is stateless between calls and cheap to keep around. Construct it
once — with whatever formatting options you need — and reuse the instance
across the application rather than building a new one per type.
</tip>

## Choosing a Printer

Both printers accept the same AST; they differ only in what they render.

|                        | [Pretty](pretty-printer.md) | [Native](native-printer.md) |
|------------------------|-----------------------------|-----------------------------|
| **Goal**               | faithful, human-readable    | valid PHP syntax            |
| `array{id: int}`       | `array{id: int}`            | `array`                     |
| `int[]`                | `int[]`                     | `iterable`                  |
| `positive-int`         | `positive-int`              | `int`                       |
| `Foo::BAR`             | `Foo::BAR`                  | `mixed`                     |
| `int<0, max>`          | `int<0, max>`               | `int`                       |
| `$arg is null ? A : B` | `($arg is null ? A : B)`    | `A│B`                       |

Reach for the **pretty** printer to display a type to a human — an error
message, a generated docblock, a diff — where every detail matters. Reach for
the **native** printer to emit something PHP can actually use — a property
type, a parameter hint, generated code.

## Formatting

Both printers share two constructor arguments, declared on the common
`TypeLang\Printer\TypePrinter` base:

<deflist>
    <def title="newLine">
        The line-break string inserted between the lines of a multi-line
        rendering. Defaults to <code>"\n"</code>.
    </def>
    <def title="indention">
        The string used for a single indentation level. Defaults to four
        spaces.
    </def>
</deflist>

```php
$printer = new PrettyTypePrinter(
    newLine: "\r\n",
    indention: "\t",
);
```

<note>
The <code>NativeTypePrinter</code> takes its type aliases as the
<b>first</b> constructor argument, so pass <code>newLine</code> and
<code>indention</code> by name — see <a href="native-printer.md">Native
Printer</a>.
</note>

## Error Handling

A printer only knows how to render the nodes that make up a valid type. If it
is handed a node it cannot render — a malformed, hand-built AST, or a node
kind it does not support — it throws a
`TypeLang\Printer\Exception\NonPrintableNodeException`.

```php
try {
    echo $printer->print($node);
} catch (\TypeLang\Printer\Exception\PrinterExceptionInterface $e) {
    // Every exception the component throws implements this interface.
}
```

An AST produced by the [parser](parser.md) is always printable, so this only
becomes a concern when constructing or rewriting the node graph by hand.

## What's Next

<deflist>
<def title="Pretty Printer">

The high-fidelity renderer and its formatting options — union and
intersection spacing, callable return types, and multi-line shapes. See
[](pretty-printer.md).

</def>
<def title="Native Printer">

The PHP-compatible renderer: how each unsupported construct is approximated,
and the built-in and custom type aliases it applies. See
[](native-printer.md).

</def>
</deflist>
