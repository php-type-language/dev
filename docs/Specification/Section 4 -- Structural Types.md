# Structural Types

This section defines the structural type forms — _shapes_ and _callables_.
Each of these forms builds on the [named type](#sec-Named-Types) grammar
introduced in the previous section.

## Shape Types

ShapeFields : `{` (ShapeBody `,`?)? `}`

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

A trailing comma MAY follow the last field or the unsealed marker.

**Counter-example.** An empty shape body carries nothing a trailing comma
could follow.

```typescript counter-example
array{,}
```

```
ParseException: Invalid shape, a field, "," or "}" is expected
```

### Shape Fields

ShapeField : ExplicitField | ImplicitField

ExplicitField : ShapeKey `?`? `:` ShapeValue

ImplicitField : ShapeValue

ShapeKey :

- ClassConstant
- Name
- IntLiteral
- StringLiteral

ShapeValue : Type

A shape field is either _explicit_ (a key, a colon, and a value type) or
_implicit_ (a value type alone, whose key is assigned positionally). A shape
key may be a bare name, an integer literal, a string literal, or a
[class constant](#sec-Class-Constants) reference. A bare name is any {Name},
and may be qualified or carry dashes, so `Path\To\KEY` and `some-key` are both
keys.

A [constant mask](#sec-Constant-Masks) is not a shape key.

Note: A {Name} and a {ClassConstant} begin with the same {Name}. As in
[Primary Types](#sec-Primary-Types), these are distinguished by what follows:
a bare {Name} key is one not followed by `::`.

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
array{ Path\To\ClassName::CONSTANT_NAME: First, \Path\To\CONSTANT_NAME: Second }
```

**Counter-example.** A mask denotes a family of constants, not one key.

```typescript counter-example
array{ JSON_*: Type }
```

```
ParseException: Invalid shape, a field, "," or "}" is expected
```

**Mixed keys are permitted.** A single shape MAY contain both explicit and
implicit fields, in any order. What keys the implicit fields denote is not a
matter of the syntax and is left to whoever interprets the shape.

```typescript
array{ First, named: Second }
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

CallableReturnType : `:` UnaryType

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

The return type is a {UnaryType}. A union, an intersection or a condition
written behind it is not a part of the return type, but of the type the
callable itself is an operand of, so `callable(): A|B` denotes a union of
`callable(): A` and `B`. A return type of that kind MUST be grouped with
parentheses.

```typescript
callable(): (A|B)
```

### Template Parameters

TemplateParameters : `<` TemplateParameter (`,` TemplateParameter)\* `,`? `>`

TemplateParameter : Variance? Identifier UpperBound? LowerBound? TemplateDefault?

UpperBound : UpperBoundOperator Type

UpperBoundOperator : one of `of` `as`

LowerBound : `super` Type

TemplateDefault : `=` Type

A callable type MAY declare the _template parameters_ it introduces, written
as a `<...>` list between the name and the parameter list. Each parameter is
an {Identifier}, optionally preceded by a {Variance} (see
[Template Argument Variance](#sec-Template-Argument-Variance)), followed by
the _bounds_ that are put on it.

A parameter accepts three kinds of limit, each of which MUST be written at
most once and in the order of the grammar above: the upper bound, then the
lower bound, then the default. A bound standing behind a {TemplateDefault}
belongs to the {Type} of the default rather than to the parameter, and is
refused.

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

```typescript
callable<covariant T>(T): void
```

**Counter-example.** A word that bounds nothing is not an operator.

```typescript counter-example
callable<T whatever Some>(): void
```

```
ParseException: Invalid template arguments, "," or ">" is expected
```

**Counter-example.** Neither is a word that is spelled the way an operator is
spelled, but written in a case of its own.

```typescript counter-example
callable<T OF Some>(): void
```

```
ParseException: Invalid template arguments, "," or ">" is expected
```

**Counter-example.** Each kind of limit is written at most once.

```typescript counter-example
callable<T of Some as Any>(): void
```

```
ParseException: Invalid template arguments, "," or ">" is expected
```

**Counter-example.** The upper bound is written before the lower one.

```typescript counter-example
callable<T super Any of Some>(): void
```

```
ParseException: Invalid template arguments, "," or ">" is expected
```

**Counter-example.** A bound does not stand behind a default.

```typescript counter-example
callable<T = int of Some>(): void
```

```
ParseException: Invalid template arguments, "," or ">" is expected
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
ParseException: Invalid template arguments, "," or ">" is expected
```

### Callable Parameters

CallableParameter :

- Type `&`? `...` Variable?
- Type `&`? Variable? DefaultValue?
- `...` Type
- `&`? `...` Variable
- `&`? Variable DefaultValue?

DefaultValue : `=` DefaultOperand?

DefaultOperand :

- BoolLiteral
- NullLiteral
- IntLiteral
- FloatLiteral
- StringLiteral
- Name
- ClassConstant

A parameter is described by its type, optionally followed by the reference
and the variadic markers, by a name and by the default value — in that order
and in no other. A parameter MAY also be given by its name alone, without a
type, and a variadic parameter MAY be given by the type behind the ellipsis.

```typescript
callable(Type)
```

```typescript
callable(Type $name)
```

```typescript
callable($name)
```

**Named Parameters.** A name beginning with `$` MAY follow the parameter's
type, [permitting the argument to be passed by name](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments),
exactly as in PHP.

```typescript
callable(A $a, B, C)
```

**Output Parameters.** An ampersand (`&`) placed _after_ the parameter type
marks the parameter as passed by reference (an _output_ parameter). A
parameter given by its name alone carries the ampersand in front of that name.

```typescript
callable(T&)
```

```typescript
callable(T &$name)
```

```typescript
callable(&$name)
```

**Counter-example.** The ampersand must follow the type; it must not precede
it.

```typescript counter-example
callable(&T)
```

```
ParseException: Invalid callable parameters, a parameter, "," or ")" is expected
```

**Optional Parameters.** A trailing `=` marks a parameter as optional: the
caller MAY omit the corresponding argument. The `=` MAY be followed by the
default value the parameter takes: a literal, the name of a global constant
or a {ClassConstant}. A default value is not an expression, and carries no
type of its own. As everywhere else, a `true`, `false` or `null` is the
literal, and not a constant of that name.

```typescript
callable(T=)
```

```typescript
callable(T &$name=)
```

```typescript
callable(T $name = 42)
```

```typescript
callable(T $name = null)
```

```typescript
callable(T $name = Path\To\CONSTANT_NAME)
```

```typescript
callable(T $name = ClassName::CONSTANT_NAME)
```

```typescript
callable($name = 'text')
```

**Counter-example.** A default value is a literal or a constant, and a mask
of constants is neither.

```typescript counter-example
callable(T $name = ClassName::*)
```

```
ParseException: Invalid callable parameters, a parameter, "," or ")" is expected
```

**Variadic Parameters.** An ellipsis (`...`) marks the parameter as variadic.
It is written after the parameter type, or, for a parameter given by its name
alone, in front of the name. A type MAY also stand behind the ellipsis. Where
a parameter carries both the ampersand and the ellipsis, the ampersand comes
first.

```typescript
callable(T ...$name)
```

```typescript
callable(T &...$name)
```

```typescript
callable(T ...)
```

```typescript
callable(...T)
```

```typescript
callable(...$name)
```

**Counter-example.** An ellipsis is followed by a type or a name, or follows
the type; it does not stand alone.

```typescript counter-example
callable(...)
```

```
ParseException: Invalid callable parameters, a parameter, "," or ")" is expected
```

**Counter-example.** A variadic parameter is already optional and therefore
must not additionally carry a default value.

```typescript counter-example
callable(T ...$name=)
```

```
ParseException: Invalid callable parameters, a parameter, "," or ")" is expected
```
