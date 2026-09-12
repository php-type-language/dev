# Partial Parsing

<primary-label ref="parser-component"/>
<link-summary>
Read as much of a type declaration as the grammar describes instead of failing
on the first syntax error, recover the offset the reading stopped at, or ask
whether a source is a type at all.
</link-summary>
<show-structure for="chapter" depth="2"/>

Partial parsing allows reading a type grammar embedded in other arbitrary
data, without requiring the full input to be a valid type statement.

This is convenient, for example, for analyzing phpdoc (docblocks): a
`@return` annotation contains a type declaration followed by a free-text
description, and there is no delimiter between the two other than "the type
grammar stops making sense here".

Unlike `parse()`, which either returns a fully valid `TypeNode` or throws,
`TypeParser::partial()` always returns a
`TypeLang\Parser\Partial\ParsedResult`, and which of the three it is says how
much of the source the grammar described.

<deflist>
<def title="SuccessfulParsedResult">

The source is a type whole, so the reading stopped at the very end of it.
Carries the `$type` built out of it and the `$offset` the reading stopped at.

</def>
<def title="PartialParsedResult">

The grammar described the beginning of the source alone. This is a successful
result as well, since a type has been built either way — the rest of the
source simply begins at the `$offset`.

</def>
<def title="FailureParsedResult">

The source opens no type at all, so nothing has been built of it. Carries the
`$message` of what stands in the way, the `$position` (line and column) and
the `$offset` the reading stopped at.

</def>
</deflist>

## Basic Usage

```php
use TypeLang\Parser\TypeParser;

$parser = new TypeParser();

$result = $parser->partial('int and more text');

var_dump($result::class);                  // PartialParsedResult
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
> //   Syntax error, unexpected "and " (T_NAME_WITH_SPACE),
> //   T_NS_DELIMITER expected in "int and more text"
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

$result = $parser->partial($content);

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
// string(35) "Returns something very interesting!"
```

## Recovering from Incomplete Constructs

Partial parsing does not attempt to *repair* broken syntax — it stops at the
last point where a complete sub-rule was matched and treats everything past
it as trailing content, however implausible that trailing content looks.

```php
$result = $parser->partial('array{');

var_dump($result->type->name->toString()); // string(5) "array"
var_dump($result->offset);                 // int(5)
```

Here the unterminated `{` of the shape-fields list is never entered, so the
parser falls back to the last valid statement — the bare `array` named type —
and reports offset `5`, right before the `{`.

## Validation

Where the type itself is of no use and the only question is whether the
source is one, `TypeParser::validate()` asks exactly that and builds nothing.
It returns a `TypeLang\Parser\Validation\CheckResult`, and which of the three
it is answers the question.

<deflist>
<def title="SuccessfulCheckResult">

The source is a type whole, so nothing stands in the way of it.

</def>
<def title="FailureCheckResult">

The source is no type of its own. Carries the `$message` of what stands in
the way, the `$position` (line and column) and the `$offset` the reading
stopped at.

</def>
<def title="PartialCheckResult">

The grammar described the beginning of the source alone. This is a failure as
well, since a check asks about the source whole, and the rest of it begins at
the `$offset`.

</def>
</deflist>

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\Validation\SuccessfulCheckResult;

$parser = new TypeParser();

var_dump($parser->validate('array{ field: result }')::class);
// TypeLang\Parser\Validation\SuccessfulCheckResult

var_dump($parser->validate('array{ field: result } and more')::class);
// TypeLang\Parser\Validation\PartialCheckResult

var_dump($parser->validate('array{')::class);
// TypeLang\Parser\Validation\PartialCheckResult

// A check for a source that is a type whole:
$isType = $parser->validate($source) instanceof SuccessfulCheckResult;
```

> A `PartialCheckResult` **is** a `FailureCheckResult`, and a
> `PartialParsedResult` **is** a `SuccessfulParsedResult`. Each hierarchy is
> shaped around the question its method asks: a check asks about the source
> whole, whereas a partial parse is content with the beginning of it.
> {style="note"}
