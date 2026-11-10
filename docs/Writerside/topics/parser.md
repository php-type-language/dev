# Type Parser Component

<primary-label ref="parser-component"/>
<link-summary>
Parses TypeLang syntax into an AST of `TypeLang\Type\*` nodes, with strict,
partial and validating reading modes, feature toggling, and grammar checking.
</link-summary>
<show-structure for="chapter" depth="2"/>

The parser component analyzes a [TypeLang](introduction.md) type declaration
string and builds an <tooltip term="AST">AST</tooltip> (Abstract Syntax Tree)
out of it, checking the grammar along the way.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/parser</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`

## Quick Start

The `TypeLang\Parser\TypeParser` class is the entry point of the component.
Its `parse()` method turns a type declaration string into a
`TypeLang\Type\TypeNode` instance (a `NamedTypeNode`, `UnionTypeNode`, etc.,
depending on the source).

```php
$parser = new TypeLang\Parser\TypeParser();

$type = $parser->parse('array{ key: int }');

var_dump($type);
```

```php
object(TypeLang\Type\NamedTypeNode)#1 (4) {
  ["offset"]=> int(0)
  ["name"]=> object(TypeLang\Type\Name)#2 (3) {
    ["offset"]=> int(0)
    ["value"]=> string(5) "array"
    ["namespace"]=> NULL
  }
  ["arguments"]=> NULL
  ["fields"]=> object(TypeLang\Type\Shape\FieldListNode)#3 (3) {
    ["offset"]=> int(7)
    ["items"]=> array(1) {
      [0]=> object(TypeLang\Type\Shape\NamedFieldNode)#4 (4) { ... }
    }
    ["isSealed"]=> bool(true)
  }
}
```
{collapsible="true" collapsed-title="Result"}

Every node exposes an `$offset` (byte offset in the source where the node
starts) and, depending on its kind, a handful of other public properties. The
node classes themselves (`TypeLang\Type\*`) belong to the separate
`type-lang/types` package — plain AST Nodes.

> If a statement cannot be parsed, `parse()` throws an exception implementing
> `TypeLang\Parser\Exception\ParserExceptionInterface`. See the [partial
> parsing](tolerant-mode.md) page for a way to read partially valid input
> instead of failing outright.
> {style="note"}

## Three Ways of Reading a Source

`TypeParser` asks the grammar three different questions, all three declared
on `TypeParserInterface`:

* `parse(): TypeNode` — strict mode. Requires the whole input to be a
  syntactically valid type statement; throws a `ParserExceptionInterface` on
  the first error.
* `partial(): ParsedResult` — reads as much of the input as the grammar
  describes and returns a `TypeLang\Parser\Partial\ParsedResult` carrying the
  type built out of the read part and the offset the reading stopped at,
  whatever follows it. Useful for phpdoc/docblock parsing, where a type
  declaration is followed by a free-text description. See
  [](tolerant-mode.md).
* `validate(): CheckResult` — tells whether the source is a type, building
  nothing of it. This is the cheapest of the three whenever the type itself
  is of no use. See [](tolerant-mode.md#validation).

## Parser Arguments

All three methods accept the source code in any of the following forms:

<tabs>
  <tab title="string">

  ```php
  $type = $parser->parse(<<<'CODE'
      object{ key?: int<0, max> }
  CODE);
  ```
  </tab>
  <tab title="resource (stream)">

  ```php
  $type = $parser->parse(
      fopen(__DIR__ . '/source.txt', 'rb'),
  );
  ```
  </tab>
  <tab title="SplFileInfo">

  ```php
  $type = $parser->parse(
      new SplFileInfo(__DIR__ . '/source.txt'),
  );
  ```
  </tab>
  <tab title="ReadableInterface">

  ```php
  $type = $parser->parse(
      Phplrt\Source\File::fromPathname(__DIR__ . '/source.txt'),
  );
  ```
  </tab>
</tabs>

## Parsing Other Constructs

By default the parser reads a whole type. Two context methods return a new
parser that starts from another grammar rule:

```php
use TypeLang\Parser\TypeParser;

$parser = new TypeParser();

// TypeLang\Type\TypeNode
$type = $parser->forType()->parse('Type<U, V>');

// TypeLang\Type\Template\TemplateParameterNode
$template = $parser->forTemplateParameter()->parse('T of U<V> = Default<Z>');
```

## Errors

Every exception thrown by the parser implements
`TypeLang\Parser\Exception\ParserExceptionInterface` and carries the
`$source` and the `$range` (`ParsingErrorRange` with `$offset` and `$length`)
of the problematic fragment.

| Exception                    | Thrown when                                            |
|------------------------------|--------------------------------------------------------|
| `UnexpectedTokenException`   | a token (or the end of input) stands where another one is expected: `Unexpected "and"` |
| `MalformedSyntaxException`   | a construct is broken: `The group is not closed properly, ")" expected` (extends `UnexpectedTokenException`) |
| `UnrecognizedSyntaxException`| a fragment is not a part of the syntax at all: `Unrecognized syntax "~"` |
| `FeatureNotEnabledException` | the syntax is [disabled](features.md): `Unions feature is not allowed` |

The first three extend `SyntaxErrorException`, and all of them extend
`ParsingException`.

## What's Next

<deflist>
<def title="Feature toggling">

Enable or disable individual language constructs (generics, shapes,
unions, ...) — see [](features.md).

</def>
<def title="Partial parsing and validation">

Read a type declaration embedded in free-form text, such as a phpdoc
annotation, or ask whether a source is a type at all — see
[](tolerant-mode.md).

</def>
<def title="Visitors">

Traverse, search, and rewrite a parsed AST — see [](visitors.md).

</def>
<def title="Name resolution">

Resolve short/relative names in an AST against `use` statements or any other
custom rule — see [](type-resolver.md).

</def>
</deflist>
