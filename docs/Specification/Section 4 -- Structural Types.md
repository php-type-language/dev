# Structural Types

This section defines the structural type forms — _shapes_ and _callables_.
Each of these forms builds on the [named type](#sec-Named-Types) grammar
introduced in the previous section.

## Shape Types

ShapeFields : `{` ShapeBody? `,`? `}`

ShapeBody :

- ShapeFieldList (`,` UnsealedShape)?
- UnsealedShape

ShapeFieldList : ShapeField (`,` ShapeField)\*

A _shape_ rigidly describes the individual elements of a composite value,
such as the keys of an array or the properties of an object. A shape is
written as a [named type](#sec-Named-Types) immediately followed by a
brace-enclosed body; the name — commonly `array`, `object`, or `list`, but
any name is accepted — describes the container, and the body describes its
elements.

```typescript
array{
    a: First,
    b: Second
}
```

```typescript
Custom\Type{
    id: int,
    name: string
}
```

An empty shape body describes a container with no elements:

```typescript
array{}
```

### Shape Fields

ShapeField : ExplicitField | ImplicitField

ExplicitField : ShapeKey `?`? `:` ShapeValue

ImplicitField : ShapeValue

ShapeKey :

- Identifier
- IntLiteral
- StringLiteral
- ConstantMask
- ClassConstant

ShapeValue : Type

A shape field is either _explicit_ (a key, a colon, and a value type) or
_implicit_ (a value type alone, whose key is assigned positionally). A shape
key may be a bare identifier, an integer literal, a string literal, or a
[constant](#sec-Constant-Types) reference — that is, a {ClassConstant} or a
{ConstantMask}.

Note: An {Identifier}, a {ClassConstant} and a {ConstantMask} may each begin
with the same {Name}. As in [Primary Types](#sec-Primary-Types), these are
distinguished by what follows: a bare {Identifier} key is one not followed by
`::` or a trailing `*`.

```typescript
array{ name: First, count: Second }
```

```typescript
array{ 1: First, 42: Second }
```

```typescript
array{ "name-some": First, "escape\nchars": Second }
```

```typescript
array{ First, Second }
```

```typescript
array{ Path\To\ClassName::CONSTANT_NAME: First, JSON_*: Second }
```

**Mixed keys are not permitted.** A single shape MUST use either explicit
keys throughout or implicit keys throughout.

```typescript counter-example
array{ named: First, Second }
```

```
ParseException: Cannot mix explicit and implicit shape keys
```

**Duplicate explicit keys are not permitted.** No two explicit fields of the
same shape may denote the same key, regardless of which of the five key forms
each uses.

```typescript counter-example
array{ 1: int, 2: int, 1: string }
```

```
ParseException: Duplicate key "1"
```

### Optional Fields

An explicit field MAY be marked _optional_ by placing a question mark before
the colon. An optional key (`key?: Type`) states that the field may be
absent; this is distinct from an optional value (`key: ?Type`), which states
that the field is always present but its value may be `null`.

```typescript
array{ key?: Type }
```

```typescript
array{ key: ?Type }
```

### Unsealed Shapes

UnsealedShape : `...` TemplateArguments?

By default, a shape is _sealed_: it describes its container exactly, and no
additional elements are permitted. A trailing ellipsis (`...`) makes the
shape _unsealed_, allowing elements beyond those listed.

```typescript
array{ key: Type, ... }
```

An unsealed shape MAY also stand alone, describing a container constrained
only by its (absent) field list:

```typescript
array{ ... }
```

An unsealed marker MAY carry [template arguments](#sec-Generic-Types) that
describe the type of the additional elements — and, optionally, of their
keys — using the same angle-bracket syntax as generics:

```typescript
array{ user: User, ...<string, object> }
```

## Callable Types

CallableType : Name `(` CallableParameters? `)` CallableReturnType?

CallableParameters : CallableParameter (`,` CallableParameter)\* `,`?

CallableReturnType : `:` Type

A _callable type_ describes a function-like value. It is a
[name](#sec-Names-and-Namespaces) — commonly `callable` or `Closure`, but any
name is accepted — followed by a parenthesized, possibly empty, parameter
list, and an optional return type introduced by a colon.

```typescript
callable()
```

```typescript
callable(): void
```

```typescript
Closure(int<0, max>, callable(?C): mixed): void
```

### Callable Parameters

CallableParameter : Type `&`? `...`? Variable? `=`?

A parameter is described by its type, optionally followed by the reference
and the variadic markers, by a name and by the default marker — in that
order and in no other. The type is the only required part.

```typescript
callable(Type)
```

```typescript
callable(Type $name)
```

**Counter-example.** A parameter given by name alone, without a type, is
not permitted.

```typescript counter-example
callable($name)
```

```
ParseException: Syntax error, unexpected ")"
```

**Named Parameters.** A name beginning with `$` MAY follow the parameter's
type, [permitting the argument to be passed by name](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments),
exactly as in PHP.

```typescript
callable(A $a, B, C)
```

**Output Parameters.** An ampersand (`&`) placed _after_ the parameter type
marks the parameter as passed by reference (an _output_ parameter).

```typescript
callable(T&)
```

```typescript
callable(T &$name)
```

**Counter-example.** The ampersand must follow the type; it must not precede
it.

```typescript counter-example
callable(&T)
```

```
ParseException: Syntax error, unexpected "&"
```

**Optional Parameters.** A trailing `=` marks a parameter as optional: the
caller MAY omit the corresponding argument.

```typescript
callable(T=)
```

```typescript
callable(T &$name=)
```

**Variadic Parameters.** An ellipsis (`...`) placed after the parameter type
marks the parameter as variadic. Where a parameter carries both markers, the
ampersand comes first.

```typescript
callable(T ...$name)
```

```typescript
callable(T &...$name)
```

**Counter-example.** The ellipsis must follow the type; it must not precede
it.

```typescript counter-example
callable(...T)
```

```
ParseException: Syntax error, unexpected "..."
```

**Counter-example.** A variadic parameter is already optional and therefore
must not additionally carry a default marker.

```typescript counter-example
callable(T ...$name=)
```

```
ParseException: Cannot have variadic param with a default
```
