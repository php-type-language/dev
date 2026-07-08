# Pretty Printer

<primary-label ref="printer-component"/>
<link-summary>
The high-fidelity printer that renders a type as faithfully as possible, with
options for spacing and multi-line shapes.
</link-summary>
<show-structure for="chapter" depth="2"/>

`TypeLang\Printer\PrettyTypePrinter` renders a type as faithfully as the AST
allows: nothing is dropped or approximated, and the result parses back into an
equivalent tree. It is the printer to reach for whenever a human reads the
output — an exception message, a generated docblock, a diff between two types.

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
parentheses only where precedence requires it, and breaks large shapes across
several lines. Every part of that behavior is adjustable through the
constructor.

## Options

All options are constructor arguments; pass the ones you need by name.

```php
$printer = new PrettyTypePrinter(
    newLine: "\n",
    indention: '    ',
    wrapUnionType: false,
    wrapIntersectionType: true,
    wrapCallableReturnType: true,
    multilineShape: 1,
);
```

### Union Spacing

`wrapUnionType` (default `false`) controls the whitespace around the `|` of a
union type.

<tabs>
<tab title="false (default)">

```php
$printer = new PrettyTypePrinter(wrapUnionType: false);

echo $printer->print($parser->parse('int|string|null'));
// int|string|null
```

</tab>
<tab title="true">

```php
$printer = new PrettyTypePrinter(wrapUnionType: true);

echo $printer->print($parser->parse('int|string|null'));
// int | string | null
```

</tab>
</tabs>

### Intersection Spacing

`wrapIntersectionType` (default `true`) does the same for the `&` of an
intersection type.

<tabs>
<tab title="true (default)">

```php
$printer = new PrettyTypePrinter(wrapIntersectionType: true);

echo $printer->print($parser->parse('Countable&Traversable'));
// Countable & Traversable
```

</tab>
<tab title="false">

```php
$printer = new PrettyTypePrinter(wrapIntersectionType: false);

echo $printer->print($parser->parse('Countable&Traversable'));
// Countable&Traversable
```

</tab>
</tabs>

### Callable Return Type

`wrapCallableReturnType` (default `true`) controls whether a space follows the
colon before a callable's return type.

<tabs>
<tab title="true (default)">

```php
$printer = new PrettyTypePrinter(wrapCallableReturnType: true);

echo $printer->print($parser->parse('callable(int):void'));
// callable(int): void
```

</tab>
<tab title="false">

```php
$printer = new PrettyTypePrinter(wrapCallableReturnType: false);

echo $printer->print($parser->parse('callable(int):void'));
// callable(int):void
```

</tab>
</tabs>

### Multi-line Shapes

`multilineShape` (default `1`) is the threshold at which a shape — an
`array{...}`, `object{...}`, or `list{...}` — spills onto multiple lines. A
shape with **more** fields than this value is printed one field per line;
anything at or below it stays on a single line.

<tabs>
<tab title="0 elements">

```php
$printer = new PrettyTypePrinter(multilineShape: 0);

echo $printer->print($parser->parse('array{id: int}'));
echo $printer->print($parser->parse('array{id: int, name: string}'));
```

```
array{
    id: int
}

array{
    id: int,
    name: string
}
```

</tab>
<tab title="1 element (default)">

```php
$printer = new PrettyTypePrinter(multilineShape: 1);

echo $printer->print($parser->parse('array{id: int}'));
echo $printer->print($parser->parse('array{id: int, name: string}'));
```

```
array{id: int}

array{
    id: int,
    name: string
}
```

</tab>
<tab title="2 elements">

```php
$printer = new PrettyTypePrinter(multilineShape: 2);

echo $printer->print($parser->parse('array{id: int}'));
echo $printer->print($parser->parse('array{id: int, name: string}'));
```

```
array{id: int}
array{id: int, name: string}
```

</tab>
</tabs>

The `newLine` and `indention` arguments (see [](printer.md#formatting)) control
how each of those lines is broken and indented:

```php
$printer = new PrettyTypePrinter(indention: '  '); // two spaces

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
meaning, so the output stays readable while parsing back into the same tree.

* A **nested logical type** is wrapped when it sits inside another one:

  ```php
  echo $printer->print($parser->parse('A&(B|C)'));
  // A & (B | C)
  ```

* A **callable return type** that is itself a union or intersection is wrapped,
  so it is not mistaken for a continuation of the outer type:

  ```php
  echo $printer->print($parser->parse('callable(): A|B'));
  // callable(): (A | B)
  ```

* A **conditional (ternary) type** is always wrapped:

  ```php
  echo $printer->print($parser->parse('$arg is null ? int : string'));
  // ($arg is null ? int : string)
  ```

Everything else — generics, offset access, list suffixes, class constants —
is rendered verbatim:

```php
echo $printer->print($parser->parse('Collection<int, User>[]'));
// Collection<int, User>[]
```

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
