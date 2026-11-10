# Native Printer

<primary-label ref="printer-component"/>
<link-summary>
The PHP-compatible printer that reduces any type down to syntax PHP itself
accepts, applying built-in and custom type aliases.
</link-summary>
<show-structure for="chapter" depth="2"/>

`TypeLang\Printer\NativeTypePrinter` renders the closest type that PHP itself
would accept. TypeLang can describe far more than the language can express —
generics, shapes, conditional types, integer ranges — so the native printer
trades that precision for validity, collapsing every construct without a
native equivalent down to a supported approximation.

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Printer\NativeTypePrinter;

$type = new TypeParser()
    ->parse('non-empty-list<positive-int>|null');

echo new NativeTypePrinter()
    ->print($type);
// array|null
```

Use it to emit something the runtime can consume — a property type, a
parameter or return hint, generated code — rather than something a human
reads. For the latter, use the [Pretty Printer](pretty-printer.md).

It is built on the same node printers as `PrettyTypePrinter` and accepts the
same `PrinterOptions` as its **second** constructor argument. In practice
the formatting options rarely show, because the constructs that would span
several lines (shapes especially) are reduced to a single word first.

## Conversion Rules

The printer keeps whatever PHP already understands and rewrites the rest:

| TypeLang input                 | Native output            | Rule                                                      |
|--------------------------------|--------------------------|-----------------------------------------------------------|
| `array{id: int, name: string}` | `array`                  | a shape becomes its base type                             |
| `object{id: int}`              | `object`                 | —                                                         |
| `callable(int $a): void`       | `callable`               | a callable loses its signature                            |
| `int[]`                        | `iterable`               | a list suffix becomes `iterable`                          |
| `Collection[Key]`              | `mixed`                  | offset access is not expressible                          |
| `Foo::BAR`, `Foo::*`           | `mixed`                  | class constants and masks become `mixed`                  |
| `42`, `"foo"`, `3.14`          | `int`, `string`, `float` | a literal becomes its type                                |
| `true`, `false`                | `bool`                   | a boolean literal becomes `bool`                          |
| `?int`                         | `int│null`               | a nullable type becomes a union with `null`               |
| `$this`                        | `self`                   | —                                                         |
| `$arg is A ? B : C`            | `B│C`                    | a conditional becomes the union of its branches           |
| `true│false`                   | `bool`                   | the pair collapses to `bool`                              |
| `int│mixed`                    | `mixed`                  | any union containing `mixed` collapses to `mixed`         |

Generic arguments are dropped along with everything else — `array<int, User>`
prints as `array`, and `class-string<Foo>` as `string`. Nested conditional
types contribute the results of all their branches, so
`T is A ? B : (T is C ? D : E)` prints as `B│D│E`.

<note>
A native union always joins its members with a bare <code>|</code> and an
intersection with a bare <code>&amp;</code>, never adding spaces, since that
is the only form PHP accepts. The <code>unionSpacing</code> and
<code>intersectionSpacing</code> options of the
<a href="pretty-printer.md">pretty printer</a> have no effect here.
</note>

## Type Aliases

Much of the reduction is driven by a table of **type aliases** — a map from a
type name to the native type it stands for. This is how `positive-int` becomes
`int` and `non-empty-list` becomes `array`.

The printer preloads the built-in vocabularies of the three major static
analyzers (Phan, Psalm and PHPStan), so their custom scalar and collection
types resolve out of the box:

```php
$printer = new NativeTypePrinter();

echo $printer->print($parser->parse('positive-int'));   // int
echo $printer->print($parser->parse('class-string'));   // string
echo $printer->print($parser->parse('non-empty-list')); // array
```

<tip>
Aliases are matched case-insensitively and apply to the <b>name</b> of a named
or callable type. A name with no alias is printed as-is, so a plain class name
such as <code>App\Entity\User</code> passes through untouched.
</tip>

### Registering Your Own

Pass extra aliases as the **first** constructor argument, an iterable keyed by
the alias name. Your aliases take precedence over the built-in ones:

```php
$printer = new NativeTypePrinter(aliases: [
    'Money' => 'int',
    'custom-uuid' => 'string',
]);

// int
echo $printer->print($parser->parse('Money'));

// string
echo $printer->print($parser->parse('custom-uuid'));
```

The formatting options go second:

```php
use TypeLang\Printer\PrinterOptions;

$printer = new NativeTypePrinter(
    aliases: ['Money' => 'int'],
    options: new PrinterOptions(),
);
```

<warning>
The <a href="pretty-printer.md">pretty printer</a> takes only the options, so
its first constructor argument is <code>PrinterOptions</code>. The native
printer takes <code>aliases</code> first and <code>options</code> second. Use
named arguments to avoid mixing them up.
</warning>

The same can be done after construction, which additionally allows an alias to
a union or an intersection of several native types:

<deflist>
    <def title="addTypeAlias(string &#36;alias, string &#36;type)">
        Map an alias to a single native type.
    </def>
    <def title="addUnionTypeAlias(string &#36;alias, array &#36;types)">
        Map an alias to a union of native types (joined with <code>|</code>,
        sorted alphabetically).
    </def>
    <def title="addIntersectionTypeAlias(string &#36;alias, array &#36;types)">
        Map an alias to an intersection of native types (joined with
        <code>&amp;</code>, sorted alphabetically).
    </def>
</deflist>

```php
$printer = new NativeTypePrinter();

$printer->addTypeAlias('Money', 'int');

// int
echo $printer->print($parser->parse('Money'));


$printer->addUnionTypeAlias('numeric-string', [
    'int',
    'string',
]);

// int|string
echo $printer->print($parser->parse('numeric-string'));


$printer->addIntersectionTypeAlias('Collection', [
    'Countable',
    'Traversable',
]);

// Countable&Traversable
echo $printer->print($parser->parse('Collection'));
```

## See Also

<deflist>
    <def title="Pretty Printer">
        Render the full, faithful type instead. See
        <a href="pretty-printer.md">Pretty Printer</a>.
    </def>
    <def title="Type Printer">
        The component overview and the shared <code>print()</code> contract.
        See <a href="printer.md">Type Printer</a>.
    </def>
</deflist>
