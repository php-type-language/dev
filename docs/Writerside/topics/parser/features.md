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
TypeLang\Parser\Exception\FeatureNotEnabledException:
Literals feature is not allowed
```

To override one or more flags on an *existing* parser, use `TypeParser::with()`
instead — it returns a new parser sharing everything else with the original one:

```php
$parser = new TypeParser();

$strict = $parser->with(
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

| Flag            | Enables syntax                              |
|-----------------|---------------------------------------------|
| `unions`        | `T\|U`                                      |
| `intersections` | `T&U`                                       |
| `nullables`     | `?T`                                        |
| `lists`         | `T[]`                                       |
| `offsets`       | `T[K]`                                      |
| `conditions`    | `T is U ? A : B`                            |
| `generics`      | `Example<T>`                                |
| `wildcards`     | `Example<*>`                                |
| `hints`         | `Example<covariant T, contravariant U>`     |
| `callables`     | `fn(): void`                                |
| `templates`     | `callable<T of U = V>(T): T`                |
| `defaults`      | `callable(T $a = U): V`                     |
| `shapes`        | `array{ key: T }`                           |
| `constants`     | `Example::CONSTANT`                         |
| `masks`         | `Example::PREFIX_*`, `Path\To\*`            |
| `literals`      | `42`, `"foo"`, `0.23`, `true`, `null`, ...  |

A flag turns off exactly one language feature. Flags do not depend on each
other: when `generics` is disabled, `Example<T>` is rejected and `wildcards`
and `hints` have nothing to apply to.

### Composite types

`unions`, `intersections`, `nullables`, `lists`, `offsets` and `conditions`
control the operators that build a type out of other types.

```php
$parser = new TypeParser(new TypeParserFeatures(
    unions: false,
));

$parser->parse('T|U'); // rejected
$parser->parse('?T');   // still allowed
```

[Conditional (ternary) type expressions](conditional-types.md) are
`T is U ? A : B`.

### Generics

`generics` is the angle-bracket argument list of a named type. Two flags
refine what an argument may be:

- `wildcards` allows `*` in place of the type: `Example<*>`;
- `hints` allows a [variance modifier](generic-types.md#call-site-hints)
  (any identifier) before the type: `Example<covariant T>`.

### Callables

`callables` is the callable (delegate) type declaration. Two flags refine it:

- `templates` allows the [template parameters](callable-types.md#template-parameters)
  of the callable: `callable<T of U>(T): T`;
- `defaults` allows a default value of a parameter: `callable(int $a = 42): void`.

### Shapes

`shapes` is the array/object shape fields: `array{ key: T }`.

### Constants

- `constants` allows a class constant as a type: `Example::CONSTANT`;
- `masks` allows a `*` in place of a part of the constant name:
  `Example::PREFIX_*`.

### Literals

[Literals are concrete values](literal-types.md) such as `42`, `0xDEAD`,
`"string"`, `0.23`, etc.

## Behavior of Disabled Features

A disabled feature is reported as a
`TypeLang\Parser\Exception\FeatureNotEnabledException` (a `ParsingException`)
by both `parse()` and `partial()`:

```php
$parser = new TypeParser(new TypeParserFeatures(unions: false));

$parser->partial('T|U');
// FeatureNotEnabledException: Unions feature is not allowed
```

> `validate()` only checks the grammar and builds nothing, so it does **not**
> take the feature flags into account: `validate('T|U')` is a
> `SuccessfulCheckResult` even when `unions` is disabled.
> {style="warning"}
