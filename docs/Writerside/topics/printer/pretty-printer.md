# Pretty Printer

<primary-label ref="printer-component"/>
<link-summary>
The high-fidelity printer that renders a type as faithfully as possible, with
options for spacing, nullable and list style, and multi-line lists.
</link-summary>
<show-structure for="chapter" depth="2"/>

`TypeLang\Printer\PrettyTypePrinter` renders a type as faithfully as the AST
allows: nothing is dropped or approximated. It is the printer to reach for
whenever a human reads the output — an exception message, a generated
docblock, a diff between two types.

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Printer\PrettyTypePrinter;

$type = new TypeParser()
    ->parse('array<non-empty-string,list<Foo>>|null');

echo new PrettyTypePrinter()
    ->print($type);
// array<non-empty-string, list<Foo>>|null
```

Out of the box it normalizes whitespace, groups nested logical types with
parentheses only where precedence requires it, and breaks shapes of more than
one field across several lines. Every part of that behavior is adjustable
through the `TypeLang\Printer\PrinterOptions` object passed to the
constructor.

## Options

The printer takes a single optional argument: a `PrinterOptions` instance.
All of its constructor arguments are optional, so pass only the ones you need
by name.

```php
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\PrinterOptions\CallableReturnSpacing;
use TypeLang\Printer\PrinterOptions\CommaSpacing;
use TypeLang\Printer\PrinterOptions\IntersectionSpacing;
use TypeLang\Printer\PrinterOptions\ListStyle;
use TypeLang\Printer\PrinterOptions\NullableStyle;
use TypeLang\Printer\PrinterOptions\UnionSpacing;

$printer = new PrettyTypePrinter(new PrinterOptions(
    newLine: "\n",
    indentation: '    ',
    unionSpacing: UnionSpacing::Compact,
    intersectionSpacing: IntersectionSpacing::Spaced,
    callableReturnSpacing: CallableReturnSpacing::Spaced,
    commaSpacing: CommaSpacing::Spaced,
    nullableStyle: NullableStyle::Question,
    listStyle: ListStyle::Brackets,
    maxInlineShapeFields: 1,
    maxInlineTemplateItems: \PHP_INT_MAX,
    maxInlineCallableParameters: \PHP_INT_MAX,
));
```

The values above are the defaults. The options object is immutable and is
shared by both printers, so the same `PrinterOptions` instance can be passed to
a `NativeTypePrinter` too.

| Option                        | Type                    | Default      |
|-------------------------------|-------------------------|--------------|
| `newLine`                     | `non-empty-string`      | `"\n"`       |
| `indentation`                 | `non-empty-string`      | four spaces  |
| `unionSpacing`                | `UnionSpacing`          | `Compact`    |
| `intersectionSpacing`         | `IntersectionSpacing`   | `Spaced`     |
| `callableReturnSpacing`       | `CallableReturnSpacing` | `Spaced`     |
| `commaSpacing`                | `CommaSpacing`          | `Spaced`     |
| `nullableStyle`               | `NullableStyle`         | `Question`   |
| `listStyle`                   | `ListStyle`             | `Brackets`   |
| `maxInlineShapeFields`        | `int<0, max>`           | `1`          |
| `maxInlineTemplateItems`      | `int<0, max>`           | `PHP_INT_MAX` |
| `maxInlineCallableParameters` | `int<0, max>`           | `PHP_INT_MAX` |

<note>
A negative <code>maxInline*</code> value makes the <code>PrinterOptions</code>
constructor throw an <code>\InvalidArgumentException</code>.
</note>

### Union Spacing

`unionSpacing` controls the whitespace around the `|` of a union type.

<tabs>
<tab title="Compact (default)">

```php
$options = new PrinterOptions(unionSpacing: UnionSpacing::Compact);

echo new PrettyTypePrinter($options)->print($parser->parse('int|string|null'));
// int|string|null
```

</tab>
<tab title="Spaced">

```php
$options = new PrinterOptions(unionSpacing: UnionSpacing::Spaced);

echo new PrettyTypePrinter($options)->print($parser->parse('int|string|null'));
// int | string | null
```

</tab>
</tabs>

### Intersection Spacing

`intersectionSpacing` does the same for the `&` of an intersection type.

<tabs>
<tab title="Spaced (default)">

```php
$options = new PrinterOptions(intersectionSpacing: IntersectionSpacing::Spaced);

echo new PrettyTypePrinter($options)->print($parser->parse('Countable&Traversable'));
// Countable & Traversable
```

</tab>
<tab title="Compact">

```php
$options = new PrinterOptions(intersectionSpacing: IntersectionSpacing::Compact);

echo new PrettyTypePrinter($options)->print($parser->parse('Countable&Traversable'));
// Countable&Traversable
```

</tab>
</tabs>

### Callable Return Type

`callableReturnSpacing` controls whether a space follows the colon before a
callable's return type.

<tabs>
<tab title="Spaced (default)">

```php
$options = new PrinterOptions(callableReturnSpacing: CallableReturnSpacing::Spaced);

echo new PrettyTypePrinter($options)->print($parser->parse('callable(int):void'));
// callable(int): void
```

</tab>
<tab title="Compact">

```php
$options = new PrinterOptions(callableReturnSpacing: CallableReturnSpacing::Compact);

echo new PrettyTypePrinter($options)->print($parser->parse('callable(int):void'));
// callable(int):void
```

</tab>
</tabs>

### Comma Spacing

`commaSpacing` controls the whitespace after the comma between the items of a
single-line list: the fields of a shape, the arguments of a template and the
parameters of a callable.

<tabs>
<tab title="Spaced (default)">

```php
$options = new PrinterOptions(commaSpacing: CommaSpacing::Spaced);

echo new PrettyTypePrinter($options)->print($parser->parse('array<int,string>'));
// array<int, string>
```

</tab>
<tab title="Compact">

```php
$options = new PrinterOptions(commaSpacing: CommaSpacing::Compact);

echo new PrettyTypePrinter($options)->print($parser->parse('array<int, string>'));
// array<int,string>
```

</tab>
</tabs>

### Nullable Style

`nullableStyle` selects how a nullable type is written.

<tabs>
<tab title="Question (default)">

```php
$options = new PrinterOptions(nullableStyle: NullableStyle::Question);

echo new PrettyTypePrinter($options)->print($parser->parse('?int'));
// ?int
```

</tab>
<tab title="Union">

```php
$options = new PrinterOptions(nullableStyle: NullableStyle::Union);

echo new PrettyTypePrinter($options)->print($parser->parse('?int'));
// int|null
```

</tab>
</tabs>

### List Style

`listStyle` selects how a list of a type is written.

<tabs>
<tab title="Brackets (default)">

```php
$options = new PrinterOptions(listStyle: ListStyle::Brackets);

echo new PrettyTypePrinter($options)->print($parser->parse('int[]'));
// int[]
```

</tab>
<tab title="Generic">

```php
$options = new PrinterOptions(listStyle: ListStyle::Generic);

echo new PrettyTypePrinter($options)->print($parser->parse('int[]'));
// array<int>
```

</tab>
</tabs>

### Multi-line Lists

Three options set how many items of a list may stay on one line. A list with
**more** items than the limit is printed one item per line; anything at or
below it stays on a single line.

<deflist>
    <def title="maxInlineShapeFields (default 1)">
        The fields of a shape: <code>array{...}</code>, <code>object{...}</code>
        or <code>list{...}</code>.
    </def>
    <def title="maxInlineTemplateItems (default PHP_INT_MAX)">
        The arguments of <code>Foo&lt;...&gt;</code> and the parameters of a
        template list. Never breaks by default.
    </def>
    <def title="maxInlineCallableParameters (default PHP_INT_MAX)">
        The parameters of a <code>callable(...)</code>. Never breaks by
        default.
    </def>
</deflist>

<tabs>
<tab title="maxInlineShapeFields: 0">

```php
$options = new PrinterOptions(maxInlineShapeFields: 0);

echo new PrettyTypePrinter($options)->print($parser->parse('array{id: int}'));
```

```
array{
    id: int
}
```

</tab>
<tab title="maxInlineShapeFields: 1 (default)">

```php
$options = new PrinterOptions(maxInlineShapeFields: 1);

echo new PrettyTypePrinter($options)->print($parser->parse('array{id: int}'));
echo new PrettyTypePrinter($options)->print($parser->parse('array{id: int, name: string}'));
```

```
array{id: int}

array{
    id: int,
    name: string
}
```

</tab>
<tab title="maxInlineShapeFields: 2">

```php
$options = new PrinterOptions(maxInlineShapeFields: 2);

echo new PrettyTypePrinter($options)->print($parser->parse('array{id: int, name: string}'));
```

```
array{id: int, name: string}
```

</tab>
</tabs>

The same rule applies to templates and callables:

```php
$options = new PrinterOptions(
    maxInlineTemplateItems: 1,
    maxInlineCallableParameters: 1,
);

$printer = new PrettyTypePrinter($options);

echo $printer->print($parser->parse('array<K, V>'));
echo $printer->print($parser->parse('callable(int, string): void'));
```

```
array<
    K,
    V
>

callable(
    int,
    string
): void
```

The `newLine` and `indentation` options control how each of those lines is
broken and indented:

```php
$printer = new PrettyTypePrinter(new PrinterOptions(indentation: '  ')); // two spaces

echo $printer->print($parser->parse('array{id: int, name: string}'));
```

```
array{
  id: int,
  name: string
}
```

## Grouping and Precedence

The pretty printer adds parentheses only where they are needed to preserve
meaning, so the output stays readable.

* A **nested logical type** is wrapped when it sits inside another one of a
  different kind:

  ```php
  echo $printer->print($parser->parse('A&(B|C)'));
  // A & (B|C)
  ```

* A **callable return type** that is itself a union or intersection is wrapped:

  ```php
  echo $printer->print($parser->parse('callable(): (A|B)'));
  // callable(): (A|B)
  ```

* A **list suffix** on a union, an intersection, a nullable type, or a
  callable with a return type wraps its base:

  ```php
  echo $printer->print($parser->parse('(int|string)[]'));
  // (int|string)[]
  ```

* A **conditional (ternary) type** is always wrapped:

  ```php
  echo $printer->print($parser->parse('$arg is null ? int : string'));
  // ($arg is null ? int : string)
  ```

Everything else — generics, variance and wildcard arguments, offset access,
class constants and masks, callable template parameters and default values —
is rendered verbatim:

```php
echo $printer->print($parser->parse('Collection<int, User>[]'));
// Collection<int, User>[]

echo $printer->print($parser->parse('array<covariant K, V>'));
// array<covariant K, V>

echo $printer->print($parser->parse('callable<T>(T): T'));
// callable<T>(T): T
```

## Customizing Node Printers

`PrettyTypePrinter::createRegistry()` returns the
`TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry` with a printer for
every node class. Each node printer implements
`TypeLang\Printer\NodePrinterInterface`, so a registry can be extended with
`with()` and passed to the generic `TypeLang\Printer\TypePrinter` together
with the options. This is exactly how the [native printer](native-printer.md)
is built on top of the pretty one.

## See Also

<deflist>
    <def title="Native Printer">
        Render a PHP-compatible type instead. See
        <a href="native-printer.md">Native Printer</a>.
    </def>
    <def title="Type Printer">
        The component overview and the shared <code>print()</code> contract.
        See <a href="printer.md">Type Printer</a>.
    </def>
</deflist>
