# Feature Toggling

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

You can enable or disable a set of parser features if your task requires only
partial support of the functionality. Such a feature allows you to conveniently
implement more strict functionality.

> One of the feature flags is the ["tolerant" mode](tolerant-mode.md),
> but the logic and meaning of its operation is more complex than simply
> disabling some feature.
> {style="note"}

## Literals

[Literals are concrete values](literal-types.md) such as `42`, `0xDEAD`, 
`"string"`, `0.23`, etc.

To enable or disable literals, use the `literals: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    literals: false,
);

$result = $parser->parse('42');
```

```
TypeLang\Parser\Exception\ParseException:
Literal values not allowed in "42"
```

## Generics

To enable or disable template arguments (generics), use
the `generics: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    generics: false,
);

$result = $parser->parse('Example<T>');
```

```
TypeLang\Parser\Exception\ParseException:
Template arguments not allowed in "Example<T>"
```

## Hints

To enable or disable template argument hints (argument modifiers), use the 
`hints: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    hints: false,
);

$result = $parser->parse('Example<out T, in U>');
```

```
TypeLang\Parser\Exception\ParseException:
Template argument hints not allowed in "Example<out T, in U>"
```

## Lists

To enable or disable type lists (legacy/short syntax for iterable types), use
the `list: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    list: false,
);

$result = $parser->parse('Example[]');
```

```
TypeLang\Parser\Exception\ParseException:
Square bracket list types not allowed in "Example[]"
```

## Offsets

<secondary-label ref="tl1.4"/>

To enable or disable type offsets, use the `offsets: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    offsets: false,
);

$result = $parser->parse('Example[Type]');
```

```
TypeLang\Parser\Exception\ParseException:
Type offsets not allowed in "Example[Type]"
```


## Callables

To enable or disable callables (delegates), use the `callables: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    callables: false,
);

$result = $parser->parse('fn()');
```

```
TypeLang\Parser\Exception\ParseException:
Callable types not allowed in "fn()"
```

## Shapes

To enable or disable shape fields, use the `shapes: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    shapes: false,
);

$result = $parser->parse(<<<'PHP'
    array{
        foo: T,
        ...
    }
    PHP);
```

```
TypeLang\Parser\Exception\ParseException:
Shape fields not allowed in "array{\n foo: T,\n ...\n}"
```

## Unions

To enable or disable union types, use the `union: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    union: false,
);

$result = $parser->parse('T|U');
```

```
TypeLang\Parser\Exception\ParseException:
Union types not allowed in "T|U"
```

## Intersections

To enable or disable union types, use the `intersection: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    intersection: false,
);

$result = $parser->parse('T&U');
```

```
TypeLang\Parser\Exception\ParseException:
Intersection types not allowed in "T&U"
```

## Conditional

To enable or disable conditional types (expressions), use the `conditional: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    conditional: false,
);

$result = $parser->parse('T is U ? 23 : 42');
```

```
TypeLang\Parser\Exception\ParseException:
Conditional expressions not allowed in "T is U ? 23 : 42"
```

## Attributes

To enable or disable attributes, use the `attributes: bool` flag.

```php
$parser = new TypeLang\Parser\Parser(
    attributes: false,
);

$result = $parser->parse(<<<'PHP'
    array{
        #[name("new_name"), skip_when_empty]
        oldName: int,
    }
    PHP);
```

```
TypeLang\Parser\Exception\ParseException:
Shape field attributes not allowed in "array{\n #[name("new_name"), 
skip_when_empt…" (19+)
```
