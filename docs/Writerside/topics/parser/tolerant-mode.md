# Tolerant Mode

<primary-label ref="parser-component"/>
<link-summary>
Parse as much of a type declaration as possible instead of failing on the
first syntax error, and recover the offset at which parsing actually stopped.
</link-summary>
<show-structure for="chapter" depth="2"/>

The "tolerant" analysis mode allows parsing a type grammar embedded in other
arbitrary data, without requiring the full input to be a valid type
statement.

This is convenient, for example, for analyzing phpdoc (docblocks): a
`@return` annotation contains a type declaration followed by a free-text
description, and there is no delimiter between the two other than "the type
grammar stops making sense here".

Unlike `parse()`, which either returns a fully valid `TypeNode` or throws,
`TypeParser::parseTolerant()` always returns a `TypeLang\Parser\ParsedResult`
object:

```php
final readonly class ParsedResult
{
    public TypeNode $type;

    /**
     * Last processed token offset, in bytes.
     */
    public int $offset;
}
```

## Basic Usage

```php
use TypeLang\Parser\TypeParser;

$parser = new TypeParser();

$result = $parser->parseTolerant('int and more text');

var_dump($result->type->name->toString()); // string(3) "int"
var_dump($result->offset);                 // int(4)
```

`$result->offset` points right after the last byte that was actually
consumed while building `$result->type` — including any trailing
whitespace that belongs to it. Everything from that offset onward is simply
whatever was left in the source:

```php
$remainder = substr('int and more text', $result->offset);

var_dump($remainder); // string(13) "and more text"
```

> In **strict** mode, the same input is rejected outright:
> ```php
> $parser->parse('int and more text');
> // TypeLang\Parser\Exception\UnexpectedTokenException:
> //   Syntax error, unexpected "and " in "int and more text" 
> //   at column 5
> ```
> {style="note"}

## Parsing a Docblock Annotation

Let's parse the contents of a "`@return`" docblock. The same technique
applies to any other annotation that embeds a type followed by free text.

```php
$parser = new TypeParser();

$content = <<<'PHP'
    @return Example<T> Returns something very interesting!
    PHP;

// There is no need to strip the description ourselves,
// only the tag name:
$content = substr($content, strlen('@return '));
// "Example<T> Returns something very interesting!"

$result = $parser->parseTolerant($content);

var_dump($result->type);
```

```php
TypeLang\Type\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Type\Name { ... "Example" ... }
  +arguments: TypeLang\Type\Template\TemplateArgumentListNode {
    +items: array:1 [
      0 => TypeLang\Type\Template\TemplateArgumentNode {
        +value: TypeLang\Type\NamedTypeNode { ... "T" ... }
        ...
      }
    ]
  }
  +fields: null
}
```
{collapsible="true" collapsed-title="Result"}

The description can then be recovered from the reported offset:

```php
$description = substr($content, $result->offset);

var_dump($description);
// string(36) "Returns something very interesting!"
```

## Recovering from Incomplete Constructs

Tolerant mode does not attempt to *repair* broken syntax — it stops at the
last point where a complete sub-rule was matched and treats everything past
it as trailing content, however implausible that trailing content looks.

```php
$result = $parser->parseTolerant('array{');

var_dump($result->type->name->toString()); // string(5) "array"
var_dump($result->offset);                 // int(5)
```

Here the unterminated `{` of the shape-fields list is never entered, so the
parser falls back to the last valid statement — the bare `array` named type —
and reports offset `5`, right before the `{`.
