# Feature Toggling

<primary-label ref="parser-component"/>
<link-summary>
Enable or disable individual grammar constructs on the TypeParser to
implement a stricter subset of the TypeLang syntax.
</link-summary>
<show-structure for="chapter" depth="2"/>

You can enable or disable a set of parser features if your task requires only
partial support of the functionality. This is convenient for implementing a
stricter subset of the syntax — for example, allowing plain PHP type
declarations while rejecting PHPStan/Psalm-style generics.

## Configuring Features

All flags live on a single immutable value object, `TypeLang\Parser\TypeParserFeatures`,
which is the first constructor argument of `TypeParser`. Every flag defaults
to `true`.

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserFeatures;

$parser = new TypeParser(new TypeParserFeatures(
    literals: false,
));

$parser->parse('42');
```

```
TypeLang\Parser\Exception\SemanticParseException:
Literal values not allowed in "42" at column 1
```

To override one or more flags on an *existing* parser, use `TypeParser::withFeatures()`
instead — it returns a new parser sharing everything else with the original one:

```php
$parser = new TypeParser();

$strict = $parser->withFeatures(
    generics: false,
    shapes: false,
);
```

`TypeParserFeatures` itself also has a `with()` method for deriving a new
feature set from an existing one, keeping every flag not explicitly passed:

```php
$features = new TypeParserFeatures()
    ->with(unions: false);
```

## Available Features

| Flag             | Enables syntax                     | Error message fragment                   |
|------------------|------------------------------------|------------------------------------------|
| `literals`       | `42`, `"foo"`, `true`, `null`, ... | `Literal values not allowed`             |
| `generics`       | `Example<T>`                       | `Template arguments not allowed`         |
| `hints`          | `Example<out T, in U>`             | `Template argument hints not allowed`    |
| `lists`          | `Example[]`                        | `Square bracket list types not allowed`  |
| `offsets`        | `Example[Type]`                    | `Type offsets not allowed`               |
| `callables`      | `fn(): void`                       | `Callable types not allowed`             |
| `shapes`         | `array{ key: T }`                  | `Shape fields not allowed`               |
| `unions`         | `T\|U`                             | `Union types not allowed`                |
| `intersections`  | `T&U`                              | `Intersection types not allowed`         |
| `conditions`     | `T is U ? A : B`                   | `Conditional expressions not allowed`    |
| `attributes`     | `#[attr]` on fields/args/params    | see below                                |

### Literals

[Literals are concrete values](literal-types.md) such as `42`, `0xDEAD`,
`"string"`, `0.23`, etc.

```php
$parser = new TypeParser(new TypeParserFeatures(
    literals: false,
));

$parser->parse('42');
```

```
Literal values not allowed in "42" at column 1
```

### Generics

Template arguments (generics), i.e. the angle-bracket argument list of a
named type.

```php
$parser = new TypeParser(new TypeParserFeatures(
    generics: false,
));

$parser->parse('Example<T>');
```

```
Template arguments not allowed in "Example<T>" at column 8
```

### Hints

Template argument hints (variance modifiers) such as `out`/`in`.

```php
$parser = new TypeParser(new TypeParserFeatures(
    hints: false,
));

$parser->parse('Example<out T, U>');
```

```
Template argument hints not allowed in "Example<out T, U>" at column 9
```

### Lists

The legacy square-bracket shorthand for iterable types.

```php
$parser = new TypeParser(new TypeParserFeatures(
    lists: false,
));

$parser->parse('Example[]');
```

```
Square bracket list types not allowed in "Example[]" at column 1
```

### Offsets

<secondary-label ref="tl1.4"/>

Type offset access, i.e. reading the value type of key out of another type.

```php
$parser = new TypeParser(new TypeParserFeatures(
    offsets: false,
));

$parser->parse('Example[Type]');
```

```
Type offsets not allowed in "Example[Type]" at column 1
```

### Callables

Callable (delegate) type declarations.

```php
$parser = new TypeParser(new TypeParserFeatures(
    callables: false,
));

$parser->parse('fn(): void');
```

```
Callable types not allowed in "fn(): void" at column 1
```

### Shapes

Array/object shape fields.

```php
$parser = new TypeParser(new TypeParserFeatures(
    shapes: false,
));

$parser->parse(<<<'PHP'
    array{foo: T}
    PHP);
```

```
Shape fields not allowed in "array{foo: T}" on line 1 at column 6
```

### Unions

```php
$parser = new TypeParser(new TypeParserFeatures(
    unions: false,
));

$parser->parse('T|U');
```

```
Union types not allowed in "T|U" at column 1
```

### Intersections

```php
$parser = new TypeParser(new TypeParserFeatures(
    intersections: false,
));

$parser->parse('T&U');
```

```
Intersection types not allowed in "T&U" at column 1
```

### Conditions

[Conditional (ternary) type expressions](conditional-types.md), i.e.
`T is U ? A : B`.

```php
$parser = new TypeParser(new TypeParserFeatures(
    conditions: false,
));

$parser->parse('T is U ? 23 : 42');
```

```
Conditional expressions not allowed in "T is U ? 23 : 42" at column 1
```

### Attributes

Unlike the other flags, `attributes` is checked independently at every
place attributes may occur — currently on
[shape fields](shape-types.md#attributes), on
[template arguments](generic-types.md#attributes), and on
[callable parameters](callable-types.md#attributes) — each reporting its own
message.

```php
$parser = new TypeParser(new TypeParserFeatures(
    attributes: false,
));

$parser->parse(<<<'PHP'
    array{
        #[name("new_name"), skip_when_empty]
        oldName: int,
    }
    PHP);
```

```
Shape field attributes not allowed in "array{\n #[name("new_name"),
…" (33+) on line 2 at column 5
```

```php
$parser->parse('Collection<#[a] T>');
```

```
Template argument attributes not allowed in "Collection<#[a] T>" 
at column 12
```

```php
$parser->parse('callable(#[a] int $x): void');
```

```
Callable parameter attributes not allowed in "callable(#[a] int $x): void" 
at column 10
```
