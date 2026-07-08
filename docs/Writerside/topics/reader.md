# Type Reader Component

<primary-label ref="reader-component"/>
<link-summary>
Reads the native PHP type declarations off Reflection objects — constants,
properties, return types and parameters — and turns them into a
`TypeLang\Type\*` AST.
</link-summary>
<show-structure for="chapter" depth="2"/>

The reader component bridges PHP's [Reflection API](https://www.php.net/manual/en/book.reflection.php)
to the [TypeLang](introduction.md) AST. Given a reflected constant, property,
function or parameter, it reads the **native** type declared on it and returns
the matching `TypeLang\Type\TypeNode` — the very same node graph the
[parser](parser.md) produces and the [printer](printer.md) renders.

That makes it the entry point whenever a type originates from real PHP code
rather than a string: read it once into an AST, then traverse, rewrite or print
it with the rest of the toolkit.

<note>
The reader works purely from Reflection, so it sees only what PHP itself
records — the native type hints. It does not parse docblocks; a
<code>@var list&lt;User&gt;</code> is invisible to it. Reading types out of
phpdoc is the job of the <a href="phpdoc.md">PHPDoc parser</a>.
</note>

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/reader</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.4`
* `ext-mbstring` <sup>optional</sup>

## Quick Start

`TypeLang\Reader\ReflectionReader` is the entry point. Each of its methods
takes a Reflection object and returns a `TypeNode`, or `null` when the element
carries no type declaration at all.

```php
use TypeLang\Reader\ReflectionReader;

final class Example
{
    public const int MAX = 100;
}

$reader = new ReflectionReader();

$type = $reader->findConstantType(new ReflectionClassConstant(
    class: Example::class, 
    constant: 'MAX',
));

var_dump($type);
```

```php
object(TypeLang\Type\NamedTypeNode) {
  ["offset"]=> int(0)
  ["name"]=> object(TypeLang\Type\Name) {
    ["offset"]=> int(0)
    ["segments"]=> array(1) {
      [0]=> object(TypeLang\Type\Identifier) {
        ["offset"]=> int(0)
        ["value"]=> string(3) "int"
      }
    }
    ["isFullyQualified"]=> bool(false)
  }
  ["arguments"]=> NULL
  ["fields"]=> NULL
}
```
{collapsible="true" collapsed-title="TypeLang\Type\NamedTypeNode"}

The returned nodes are the plain AST objects of the `type-lang/types` package.
Throughout the rest of this page they are shown rendered back to a string with
the [pretty printer](printer.md) — `$printer->print($type)` — since that reads
more easily than a full dump:

```php
use TypeLang\Printer\PrettyTypePrinter;

// int
echo new PrettyTypePrinter()
    ->print($type); 
```

<tip>
A reader is stateless; construct one and reuse it. The same instance satisfies
<code>ReaderInterface</code> — the aggregate of the four reader interfaces
below — so it can be type-hinted wherever any one of them is expected.
</tip>

## The Reader Interface

`ReflectionReader` implements `TypeLang\Reader\ReaderInterface`, which composes
four focused interfaces — one per kind of declaration:

- `findConstantType(ReflectionClassConstant)`                     - class constant     
- `findPropertyType(ReflectionProperty, PropertyAccessDirection)` - property           
- `findFunctionType(ReflectionFunctionAbstract)`                  - function or method 
- `findParameterType(ReflectionParameter)`                        - parameter          

Every method returns `?TypeNode`: a node when a type is declared, or `null`
when it is absent. An untyped constant, a bare `$property`, a parameter with no
hint — all read back as `null` rather than as `mixed`, so a missing type is
never confused with an explicit one.

## Reading Types

### Constants

```php
final class Example
{
    public const int MAX = 100;
}

$type = $reader->findConstantType(new ReflectionClassConstant(
    class: Example::class, 
    constant: 'MAX',
));

// int
echo $printer->print($type);
```

### Properties

```php
final class User
{
    public ?DateTimeInterface $bannedAt;
}

$type = $reader->findPropertyType(new ReflectionProperty(
    class: User::class,
    property: 'bannedAt',
));

// ?\DateTimeInterface
echo $printer->print($type); 
```

A property may declare a different type for reading and for writing; which one
is returned is controlled by the second argument — see
[Read vs. Write](#read-vs-write) below.

### Functions and Methods

`findFunctionType()` reads a **return** type, from either a free function or a
method (both are a `ReflectionFunctionAbstract`).

```php
function handle(): Generator|false { /* ... */ }

$type = $reader->findFunctionType(new ReflectionFunction(
    function: 'handle',
));

echo $printer->print($type); // \Generator|false
```

### Parameters

```php
function handle(Psr\Log\LoggerInterface $logger): void { /* ... */ }

$type = $reader->findParameterType(new ReflectionParameter(
    function: 'handle', 
    param: 'logger',
));

// \Psr\Log\LoggerInterface
echo $printer->print($type);
```

## How Types Are Converted

The reader mirrors PHP's own type system into the AST, with a couple of
normalizations worth knowing:

<deflist>
    <def title="Class names become fully qualified">
        A class or interface name is emitted fully qualified, with a leading
        <code>\</code> — <code>Example</code> reads back as <code>\Example</code>.
        Built-in and special types (<code>int</code>, <code>string</code>,
        <code>static</code>, ...) stay as written.
    </def>
    <def title="Nullable types are wrapped">
        A nullable declaration becomes a <code>NullableTypeNode</code>:
        <code>?Foo</code> and <code>Foo|null</code> both read as
        <code>?\Foo</code>. The <code>null</code> and <code>mixed</code> types,
        which are nullable on their own, are left unwrapped.
    </def>
    <def title="Composite types map one-to-one">
        A union becomes a <code>UnionTypeNode</code> and an intersection an
        <code>IntersectionTypeNode</code>, each member converted by the same
        rules.
    </def>
    <def title="An absent type is null">
        When Reflection reports no type, the method returns <code>null</code>.
    </def>
</deflist>

```php
final class Repository
{
    public Countable&Traversable $items;
}

$type = $reader->findPropertyType(new ReflectionProperty(
    class: Repository::class, 
    property: 'items',
));

// \Countable & \Traversable
echo $printer->print($type);
```

## Read vs. Write {id="read-vs-write"}

Since PHP 8.4 a property can accept a wider (or narrower) type on write than it
exposes on read, by giving its `set` hook a typed parameter.
`findPropertyType()` takes a `TypeLang\Reader\PropertyAccessDirection` to pick
which side to read:

<deflist>
    <def title="PropertyAccessDirection::Read">
        The property's declared (get) type. This is the default.
    </def>
    <def title="PropertyAccessDirection::Write">
        The type accepted on assignment — the parameter type of the property's
        <code>set</code> hook, when one narrows or widens it. Falls back to the
        read type when the property has no such hook.
    </def>
</deflist>

```php
use TypeLang\Reader\PropertyAccessDirection;

final class Post
{
    public string $slug {
        set(string|Stringable $value) {
            $this->slug = (string) $value;
        }
    }
}

$property = new ReflectionProperty(Post::class, 'slug');

// string
echo $printer->print($reader->findPropertyType($property));

// string
echo $printer->print($reader->findPropertyType(
    property: $property,
    access: PropertyAccessDirection::Read, // default
));

// \Stringable|string
echo $printer->print($reader->findPropertyType(
    property: $property,
    access: PropertyAccessDirection::Write,
));
```

<note>
<code>PropertyAccessDirection::DEFAULT</code> is a constant aliasing
<code>Read</code>, so omitting the argument and passing
<code>PropertyAccessDirection::Read</code> are equivalent.
</note>

## Converting a Reflection Type Directly

When you already hold a `ReflectionType` — rather than the element it came
from — `getType()` converts it straight to a `TypeNode`:

```php
$parameter = new ReflectionParameter('handle', 'logger');

$type = $reader->getType($parameter->getType());

// \Psr\Log\LoggerInterface
echo $printer->print($type);
```

Unlike the `find*` methods, `getType()` requires a non-null `ReflectionType`;
it is the primitive the others are built on.

## Error Handling

Every exception the component throws implements
`TypeLang\Reader\Exception\ReaderExceptionInterface`. The concrete one raised
when a reflected type cannot be mapped to the AST — an unknown
`ReflectionType` implementation — is an `UnrecognizedTypeException`, rethrown by
the `find*` methods as the matching
`UnrecognizedConstantTypeException`, `UnrecognizedPropertyTypeException`,
`UnrecognizedFunctionTypeException` or `UnrecognizedParameterTypeException` so
the failing element is named in the message.

```php
use TypeLang\Reader\Exception\ReaderExceptionInterface;

try {
    $type = $reader->findParameterType($parameter);
} catch (ReaderExceptionInterface $e) {
    // Reading failed — inspect $e for the offending element.
}
```

The standard reflection type kinds — named, union and intersection — are all
supported, so in practice this only surfaces for an exotic or future
`ReflectionType` the reader does not yet understand.

## Reading a Whole Class

Putting it together — read and print every type on a class, using the
[printer](printer.md) to render each result:

```php
$class = new ReflectionClass(App\Entity\User::class);
$reader = new TypeLang\Reader\ReflectionReader();
$printer = new TypeLang\Printer\PrettyTypePrinter();

foreach ($class->getReflectionConstants() as $constant) {
    if ($type = $reader->findConstantType($constant)) {
        printf("const %s: %s\n", $constant->name, $printer->print($type));
    }
}

foreach ($class->getProperties() as $property) {
    if ($type = $reader->findPropertyType($property)) {
        printf("property $%s: %s\n", $property->name, $printer->print($type));
    }
}

foreach ($class->getMethods() as $method) {
    if ($type = $reader->findFunctionType($method)) {
        printf("method %s(): %s\n", $method->name, $printer->print($type));
    }

    foreach ($method->getParameters() as $parameter) {
        if ($type = $reader->findParameterType($parameter)) {
            printf("  param $%s: %s\n", $parameter->name, $printer->print($type));
        }
    }
}
```

## What's Next

<deflist>
<def title="Type Printer">

Render the AST the reader returns back into a string — faithfully or as a
PHP-compatible type. See [](printer.md).

</def>
<def title="Type Parser">

Read a type from a string instead of from Reflection, into the same AST. See
[](parser.md).

</def>
<def title="PHPDoc Parser">

Read the richer types written in docblocks, which Reflection cannot see. See
[](phpdoc.md).

</def>
</deflist>
