# Type Parser Component

<primary-label ref="parser-component"/>
<link-summary>
Parses TypeLang syntax into an AST of `TypeLang\Type\*` nodes, with strict and
tolerant parsing modes, feature toggling, and grammar checking.
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
* `PHP >= 8.4`

## Quick Start

The `TypeLang\Parser\TypeParser` class is the entry point of the component.
Its `parse()` method turns a type declaration string into a
`TypeLang\Type\TypeNode` instance.

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
    ["segments"]=> array(1) {
      [0]=> object(TypeLang\Type\Identifier)#3 (2) {
        ["offset"]=> int(0)
        ["value"]=> string(5) "array"
      }
    }
    ["isFullyQualified"]=> bool(false)
  }
  ["arguments"]=> NULL
  ["fields"]=> object(TypeLang\Type\Shape\FieldsListNode)#4 (3) {
    ["offset"]=> int(7)
    ["items"]=> array(1) {
      [0]=> object(TypeLang\Type\Shape\NamedFieldNode)#5 (5) { ... }
    }
    ["sealed"]=> bool(true)
  }
}
```
{collapsible="true" collapsed-title="Result"}

Every node exposes an `$offset` (byte offset in the source where the node
starts) and, depending on its kind, a handful of other public properties. The
node classes themselves (`TypeLang\Type\*`) belong to the separate
`type-lang/types` package — plain AST Nodes.

> If a statement cannot be parsed, `parse()` throws an exception implementing
> `TypeLang\Parser\Exception\ParserExceptionInterface`. See the [tolerant
> mode](tolerant-mode.md) page for a way to parse partially valid input
> instead of failing outright.
> {style="note"}

## Strict vs. Tolerant Parsing

`TypeParser` implements two parsing strategies, both declared on
`TypeParserInterface`:

* `parse(): TypeNode` — strict mode. Requires the whole input to be a
  syntactically valid type statement; throws a `ParserExceptionInterface` on
  the first error.
* `parseTolerant(): ParsedResult` — tolerant mode. Parses as much of the
  input as it can and returns a `TypeLang\Parser\ParsedResult` object
  containing the (possibly partial) type and the offset up to which the
  source was actually consumed — regardless of what follows. Useful for
  phpdoc/docblock parsing where a type declaration is followed by a
  free-text description. See [Tolerant mode](tolerant-mode.md).

## Parser Arguments

The `parse()`/`parseTolerant()` methods accept the source code in
any of the following forms:

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

## What's Next

<deflist>
<def title="Feature toggling">

Enable or disable individual language constructs (generics, shapes,
unions, ...) — see [](features.md).

</def>
<def title="Tolerant mode">

Parse a type declaration embedded in free-form text, such as a phpdoc
annotation — see [](tolerant-mode.md).

</def>
<def title="Visitors">

Traverse, search, and rewrite a parsed AST — see [](visitors.md).

</def>
<def title="Name resolution">

Resolve short/relative names in an AST against `use` statements or any other
custom rule — see [](type-resolver.md).

</def>
</deflist>
