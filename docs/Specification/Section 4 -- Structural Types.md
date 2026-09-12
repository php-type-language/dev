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

CallableType : Name TemplateParameters? `(` CallableParameters? `)` CallableReturnType?

CallableParameters : CallableParameter (`,` CallableParameter)\* `,`?

CallableReturnType : `:` Type

A _callable type_ describes a function-like value. It is a
[name](#sec-Names-and-Namespaces) — commonly `callable` or `Closure`, but any
name is accepted — optionally followed by the
[template parameters](#sec-Template-Parameters) it declares, then by a
parenthesized, possibly empty, parameter list, and an optional return type
introduced by a colon.

```typescript
callable()
```

```typescript
callable(): void
```

```typescript
Closure(int<0, max>, callable(?C): mixed): void
```

### Template Parameters

TemplateParameters : `<` TemplateParameter (`,` TemplateParameter)\* `,`? `>`

TemplateParameter : Identifier TemplateBound\* TemplateDefault?

TemplateBound :

- UpperBound
- LowerBound

UpperBound : UpperBoundOperator Type

UpperBoundOperator : one of `of` `as`

LowerBound : `super` Type

TemplateDefault : `=` Type

A callable type MAY declare the _template parameters_ it introduces, written
as a `<...>` list between the name and the parameter list. Each parameter is
an {Identifier} followed by as many _bounds_ as are put on it.

A parameter accepts three kinds of limit, each of which MUST be written at
most once. The two bounds MAY be written in either order, while a
{TemplateDefault} MUST be written last: a bound standing behind it belongs to
the {Type} of the default rather than to the parameter.

- An {UpperBound} narrows the parameter from above: the argument is to be a
  subtype of the given {Type}. Its two operators denote the same thing, and a
  conforming implementation MUST retain the one that was written.
- A {LowerBound} bounds the parameter from below: the argument is to be a
  supertype of the given {Type}.
- A {TemplateDefault} is the {Type} the parameter takes when no argument is
  supplied. It bounds nothing.

Each operator is matched the one way it is spelled, so an `OF` bounds nothing
an `of` does.

```typescript
callable<T>(T): T
```

```typescript
callable<T of Some>(T): T
```

```typescript
Closure<T of Some, U super Any, V = int>(T, U): V
```

```typescript
callable<T of Some super Any = int>(T): void
```

**Counter-example.** A word that bounds nothing is not an operator.

```typescript counter-example
callable<T whatever Some>(): void
```

```
ParseException: Template parameter cannot be bounded with "whatever", expected one of "of", "as" or "super"
```

**Counter-example.** Neither is a word that is spelled the way an operator is
spelled, but written in a case of its own.

```typescript counter-example
callable<T OF Some>(): void
```

```
ParseException: Template parameter cannot be bounded with "OF", expected one of "of", "as" or "super"
```

**Counter-example.** Each kind of limit is written at most once.

```typescript counter-example
callable<T of Some as Any>(): void
```

```
ParseException: Template parameter cannot have more than one upper bound
```

**Counter-example.** A bound does not stand behind a default.

```typescript counter-example
callable<T = int of Some>(): void
```

```
ParseException: Template parameter default must be written last, since a bound behind it reads as a bound of the default itself
```

A `<...>` list is written the same way whether it declares template parameters
or supplies [template arguments](#sec-Generic-Types), and only the `(` that
follows a parameter list tells them apart. A list that no `(` follows is
therefore a list of template arguments, which describes no bounds.

**Counter-example.** Bounds belong to a callable alone.

```typescript counter-example
Collection<T of Some>
```

```
ParseException: Syntax error, unexpected end of input
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
