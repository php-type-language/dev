# Types

A TypeLang document is a single {Type}. This section defines the syntactic
grammar of types: how the tokens produced by the
[Lexical Grammar](#sec-Lexical-Grammar) are assembled into an abstract syntax
tree.

Type : Expression

The root non-terminal of every document is {Type}, which expands to a single
{Expression}.

## Type Precedence

Expression : ConditionalType

A {Type} is parsed as an operator-precedence hierarchy. From the
loosest-binding construct to the tightest, the levels are:

1. **Conditional** (ternary) types — `… is … ? … : …`
2. **Union** types — `A | B`
3. **Intersection** types — `A & B`
4. **Nullable** prefix — `?A`
5. **Postfix** list and offset access — `A[]`, `A[K]`
6. **Primary** types — names, literals, callables, shapes, and parenthesized
   types

Each level is defined in terms of the next-tighter level, so that, for
example, intersection binds more tightly than union and `A & B | C` is parsed
as `(A & B) | C`. Parentheses (see [Primary Types](#sec-Primary-Types)) MAY be
used to override this precedence.

Note: The nullable prefix binds more loosely than the postfix suffixes, so
`?A[]` denotes a nullable list, `?(A[])`, and not a list of nullables.

## Conditional Types

ConditionalType :

- LogicalType ConditionalSuffix?
- Variable ConditionalSuffix

ConditionalSuffix : ConditionalOperator ConditionalOperand `?` Type `:` Type

ConditionalOperand :

- LogicalType
- Variable

ConditionalOperator : one of `is` `is not`

A _conditional type_ (also called a ternary type) selects between two types
based on a comparison between a _subject_ and a _comparand_. It is written as
the subject, an operator, the comparand, a `?`, the type chosen when the
condition holds, a `:`, and the type chosen otherwise.

```typescript
T is string ? int : bool
```

The negative-equality operator is spelled `is not`. It is a single token
(see [Reserved Words](#sec-Reserved-Words)), so whitespace and comments MAY
stand between its two words, but nothing else is needed to tell it from a lone
`is`.

```typescript
T is not string ? int : bool
```

Either the subject or the comparand MAY be a {Variable} instead of a type,
including in "Yoda" order, where the variable appears on the right:

```typescript
$value is array ? non-empty-array : null
```

```typescript
array is $value ? non-empty-array : null
```

The subject and the comparand are each a {LogicalType}, that is a union or
anything that binds more tightly. A conditional type is not one, so it MUST be
grouped with parentheses to stand in either place. The two branches, on the
contrary, are full {Type}s: a conditional type written as the else branch
needs no parentheses, and nests to the right.

```typescript
T is string ? int : T is bool ? float : null
```

```typescript
(T is string ? int : bool) is int ? A : B
```

**Counter-example.** The comparand is not a conditional type.

```typescript counter-example
T is U is V ? A : B
```

```
ParseException: A "?" is expected after the target of the condition
```

Note: A bare {Variable} is only a valid type when it carries a
{ConditionalSuffix}. A {Variable} MUST NOT otherwise stand alone as a type;
the only variable-like token that may appear as a primary type on its own is
`$this` (see [Primary Types](#sec-Primary-Types)).

## Logical Types

TypeLang, [like PHP](https://www.php.net/manual/en/language.types.type-system.php),
supports two composite (logical) type constructors — _union_ and
_intersection_ — together with a _nullable_ shorthand.

### Union Types

LogicalType : UnionType

UnionType : IntersectionType (`|` IntersectionType)\*

A _union type_ is a sequence of two or more member types separated by the pipe
character (`|`). It denotes a value that satisfies at least one of its
members.

```typescript
A | B | C
```

Note: A {UnionType} with a single member denotes that member directly; no
union node is produced unless at least two members are present.

### Intersection Types

IntersectionType : UnaryType (`&` UnaryType)\*

An _intersection type_ is a sequence of two or more member types separated by
the ampersand character (`&`). It denotes a value that simultaneously
satisfies every member.

```typescript
A & B & C
```

Because intersection binds more tightly than union, the two constructors may
be combined to express disjunctive and conjunctive normal forms, with
parentheses used for the opposite grouping:

```typescript
(A & B) | C
```

```typescript
(A | B) & C
```

### Nullable Types

UnaryType : NullableType

NullableType : `?`? PostfixType

A _nullable type_ is written with a leading question mark (`?`) and is
shorthand for the union of its operand with `null`; that is, `?T` denotes the
same set of values as `T | null`.

```typescript
?Example
```

**Counter-example.** The question mark must precede the type; a trailing
question mark is not a nullable type.

```typescript counter-example
Example?
```

```
ParseException: Unexpected "?"
```

## List and Offset Access Types

PostfixType : PrimaryType TypeSuffix\*

TypeSuffix :

- ListSuffix
- OffsetSuffix

ListSuffix : `[` `]`

OffsetSuffix : `[` Type `]`

A {PrimaryType} MAY be followed by zero or more postfix suffixes, applied left
to right.

### List Syntax

An empty pair of square brackets (`[]`) forms the legacy _list_ (array)
syntax. It is equivalent to wrapping the operand in a list, and may be
repeated to describe nested lists.

```typescript
User[]
```

```typescript
User[][]
```

The legacy list syntax accepts no key type between the brackets; for that,
use [offset access](#sec-Offset-Access) or the modern `array<…>` generic. A
`User[int]` is a well-formed document all the same — an _offset access_ type
(see below) rather than a list, since `int` is a valid {Type}. It is the
absence of a type between the brackets that selects the list reading:

```typescript
User[int]
```

### Offset Access

A pair of square brackets enclosing a {Type} forms an _offset access_ type,
which denotes the type of the element addressed by that offset within the
operand (typically a [shape](#sec-Shape-Types) or an array).

```typescript
T['offset']
```

```typescript
T[U]
```

```typescript
array{int, string}[0]
```

Because the offset is itself an arbitrary {Type}, any type — including
objects, shapes, or conditional types — MAY be used as a key:

```typescript
T<U>[object{key: int, ...}]
```

**Counter-example.** Each offset is enclosed in a single pair of brackets.

```typescript counter-example
Collection[[Some]]
```

```
ParseException: A type or "]" is expected after "["
```

## Primary Types

PrimaryType :

- `(` Type `)`
- ThisVariable
- LiteralType
- CallableType
- NamedType

A _primary type_ is the tightest-binding form. It is one of: a parenthesized
{Type}, the `$this` type, a literal type, a callable type (see
[Callable Types](#sec-Callable-Types)), or a named type.

A parenthesized type is used to override precedence; it denotes exactly the
type it encloses:

```typescript
(A | B) & C
```

The `$this` variable, when used as a primary type, denotes the current object
type:

```typescript
$this
```

Note: Each of {CallableType}, {NamedType}, and the {ClassConstant} and
{ClassConstantMask} forms of {LiteralType} begins with a {Name}. Once a
complete {Name} has been recognized, these are distinguished by what
immediately follows it: `::` selects a [class constant](#sec-Class-Constants)
or a class constant mask; `(` selects a [callable type](#sec-Callable-Types);
`{` selects a [shape](#sec-Shape-Types) named type; `<` selects a callable
type when the angle-bracket list is a list of
[template parameters](#sec-Template-Parameters) that a `(` follows, and a
[generic](#sec-Generic-Types) named type otherwise; any other following token
(or the end of the document) leaves a plain named type. A global
{ConstantMask} begins with a {NamespacePrefix} or an {IdentifierMask}, neither
of which is a {Name}, and is told apart by that token alone.

Note: A bare `true`, `false` or `null` is likewise both a {LiteralType} and,
through {Identifier}, a {Name} of a single segment (see
[Reserved Words](#sec-Reserved-Words)). Where a primary type begins with such
an unqualified word, the {LiteralType} is the one taken, so a `true` denotes
the boolean literal and never a type named `true`, and what follows it is not
read as a continuation of a name: `null<int>`, `true{a: int}`, `null(int)` and
`true::X` are rejected. A name that must begin with such a word is written
qualified (`\null<int>`). The other reserved words denote no literal of their
own, and nothing is therefore left to shadow the named type each of them is.

## Names and Namespaces

Name :

- NamespacePrefix Identifier
- VirtualName
- Identifier

Identifier :

- NameToken
- ReservedWord

A {Name} is a sequence of one or more segments joined by the namespace
separator (`\`), mirroring
[PHP namespaces](https://www.php.net/manual/en/language.namespaces.rationale.php).
The segments that precede the last one, together with the separators behind
them, are a single {NamespacePrefix} token (see
[Namespace Prefixes](#sec-Namespace-Prefixes)), and the last segment is an
{Identifier}. A _fully-qualified_ name additionally begins with a leading
separator. A name of dashes, such as `non-empty-string`, is a {VirtualName}
and stands alone: it has no namespace.

```typescript
Example\Name
```

```typescript
\Absolute\Type\Name
```

```typescript
non-empty-string
```

A separator MAY appear at the start of a name or between two segments, but
MUST NOT appear at the end. Neither does {Ignored} stand within a name, around
a separator or anywhere else.

**Reserved words within names.** Every reserved word — `true`, `false`,
`null`, `is`, `not`, `of`, `as`, `super`, `covariant`, `contravariant` and
`invariant` (see [Reserved Words](#sec-Reserved-Words)) — MAY appear as an
{Identifier} segment of a {Name}. In a qualified position — following a
separator or another segment, or followed by a separator — such a word is a
name segment and nothing besides. This makes `\null` a reference to a type
named `null`, distinct from the `null` literal, and `null\Some` a name in a
namespace that begins with the word, the way PHP lets one begin.

```typescript
null\Some
```

**Counter-example.** A name cannot end with a separator.

```typescript counter-example
Example\Name\
```

```
ParseException: Unrecognized syntax "\Name\"
```

**Counter-example.** No gap stands between the segments of a name.

```typescript counter-example
Example \ Name
```

```
ParseException: Unrecognized syntax "\"
```

**Counter-example.** A {VirtualName} does not take a namespace.

```typescript counter-example
Example\non-empty-string
```

```
ParseException: Unexpected "non-empty-string", expected "true", "false", "null", an identifier
```

## Named Types

NamedType : Name (TemplateArguments | ShapeFields)?

A _named type_ is a {Name} optionally followed by either a list of
[template arguments](#sec-Generic-Types) (generics) or a
[shape body](#sec-Shape-Types). A bare named type is the most common type of
all:

```typescript
Path\To\ExampleClass
```

This grammar imposes no restriction on which names are valid; `int`, `string`,
`list`, `non-empty-string`, and any user-defined class name are all parsed
identically as named types. Whether a name refers to a built-in type, a
class, an interface, an enum, or a type alias is a semantic concern for the
implementation and is outside the scope of this specification.

## Generic Types

TemplateArguments : `<` TemplateArgument (`,` TemplateArgument)\* `,`? `>`

TemplateArgument :

- Wildcard
- Variance? Type

Variance : one of `covariant` `contravariant` `invariant`

Wildcard : `*`

A _generic type_ supplies a named type with one or more _template arguments_,
each of which is itself a {Type} or a {Wildcard}, and a {Type} MAY be
preceded by a {Variance}. Arguments are enclosed in
angle brackets (`<` and `>`) and separated by commas. A trailing comma is
permitted.

Validating the number of arguments, their bounds, and their nesting is the
responsibility of the implementation, not of this grammar, which imposes no
such limits.

```typescript
Path\To\ExampleClass<T, U>
```

```typescript
iterable<int<0, max>, Collection<User>>
```

```typescript
HashMap<Request, User,>
```

**Counter-example.** At least one argument is required, and a leading comma is
not permitted.

```typescript counter-example
example<>
```

```
ParseException: A template argument is expected after "<"
```

Note: Throughout this document, the term _template argument_ refers to a
value supplied at a use site, such as the `int<0, max>` in
`iterable<int<0, max>, …>`. This is distinct from a _template parameter_,
which is part of a callable's declaration (see
[Template Parameters](#sec-Template-Parameters)).

### Template Argument Variance

A template argument MAY carry a single leading _variance_: one of the
keywords `covariant`, `contravariant` and `invariant` placed before the
argument's type. A variance is used to express
[call-site variance](https://phpstan.org/blog/guide-to-call-site-generic-variance#call-site-variance).

```typescript
HashMap<array-key, covariant Request>
```

```typescript
Handler<contravariant Request, invariant Response>
```

The three words are keywords (see [Reserved Words](#sec-Reserved-Words)) and
are matched the one way they are spelled. A variance is read only where a type
goes on behind it. Anywhere else the word is itself the argument's type, so
the `covariant` below is a type of that name, and a word of any other kind,
like `in` or `out`, is never a variance:

```typescript
Some<covariant>
```

```typescript
Some<covariant, T>
```

Each argument may carry at most one variance, and a {Wildcard} carries none.
A variance followed by a parenthesis reads as the variance of a grouped type,
and not as a callable type of that name.

```typescript
Some<covariant (A|B)>
```

**Counter-example.** A word that is not a variance cannot stand in front of a
type.

```typescript counter-example
HashMap<array-key, in Request>
```

```
ParseException: Invalid template arguments, "," or ">" is expected
```

**Counter-example.** A variance does not go together with a wildcard.

```typescript counter-example
HashMap<array-key, covariant *>
```

```
ParseException: Invalid template arguments, "," or ">" is expected
```

## Literal Types

LiteralType :

- BoolLiteral
- NullLiteral
- IntLiteral
- FloatLiteral
- StringLiteral
- ClassConstant
- ConstantMask

A _literal type_ denotes the singleton type containing exactly one value, or,
in the case of a {ConstantMask}, a well-defined family of such singleton
types. The boolean, null, integer, floating-point and string forms correspond
directly to the [literal tokens](#sec-Literal-Tokens) of the same name:

```typescript
true
```

```typescript
42
```

```typescript
"Hello World"
```

A {ClassConstant} and a {ConstantMask} (see
[Constant Types](#sec-Constant-Types), below) are likewise literal types,
since each references a single PHP constant or a well-defined family of PHP
constants. An unqualified reference to a global constant, by contrast, has no
dedicated grammar of its own; it is described in
[Global Constants](#sec-Global-Constants).

## Constant Types

TypeLang provides two dedicated grammar forms for referencing constants: a
{ClassConstant} and a {ConstantMask}. Together with the naming convention for
a single global constant described below, this specification uses the term
_constant type_ loosely to refer to all three collectively; only
{ClassConstant} and {ConstantMask}, however, are formal non-terminals of the
grammar.

Note: Because the grammar of a class or global constant name is identical to
that of a {Name}, constant names share its rules. The class of a
{ClassConstant} is a {Name}, so it cannot be one of the bare words `true`,
`false` or `null` (see [Primary Types](#sec-Primary-Types)), while the constant
itself is an {Identifier} and MAY be any reserved word, `A::true` included.
Unlike a conventional type name, a constant is conventionally written without
a dash.

### Global Constants

Syntactically, an unqualified reference to a global constant is
indistinguishable from, and MUST be parsed as, an ordinary
[named type](#sec-Named-Types) (see [Primary Types](#sec-Primary-Types)):

```typescript
JSON_THROW_ON_ERROR
```

```typescript
pcov\version
```

Whether a given {NamedType} denotes a global constant, a class, an interface,
or something else entirely is a semantic concern that this specification
leaves to the implementation.

### Class Constants

ClassConstant : Name `::` Identifier

A reference to a class constant is a {Name} (the class), the `::` separator,
and an {Identifier} (the constant). The constant segment is a single
identifier and cannot itself be namespaced.

```typescript
ClassName::CONSTANT_NAME
```

```typescript
Path\To\ClassName::ANOTHER_CONSTANT_NAME
```

**Counter-example.** The constant part may not contain a namespace separator.

```typescript counter-example
ClassName::SOME\ANY
```

```
ParseException: Unexpected "SOME\", expected an identifier, "true", "false", "null" and other
```

### Constant Masks

ConstantMask :

- GlobalConstantMask
- ClassConstantMask

GlobalConstantMask :

- IdentifierMask
- NamespacePrefix IdentifierMask
- NamespacePrefix Wildcard

ClassConstantMask : Name `::` (IdentifierMask | Wildcard)

A _constant mask_ denotes a family of constants whose names are written of the
same segments, in the same order, with a `*` standing wherever the name is
left unsaid. The name part of a mask is the single token {IdentifierMask} (see
[Mask Tokens](#sec-Mask-Tokens)), so it holds at least one `*`, no gap stands
inside it, and its segments and wildcards alternate: two wildcards in a row
denote nothing a single one does not.

A global constant mask matches every global constant the segments describe:

```typescript
JSON_*
```

The name of a global constant MAY be left unsaid from its very beginning, or
in its middle, and the namespace it belongs to MAY be written in front of it
as a {NamespacePrefix}. A prefix standing alone in front of a {Wildcard} is a
namespace whole, and the mask then matches every constant that namespace
holds:

```typescript
*_SUFFIX
```

```typescript
JSON_*_FLAG
```

```typescript
Path\To\JSON_*
```

```typescript
Path\To\*
```

A class constant mask matches every constant of a class the segments describe;
the name of the constant MAY be omitted entirely, in which case the mask
matches every constant of the class:

```typescript
Path\To\ClassName::PREFIX_*
```

```typescript
Path\To\ClassName::*_SUFFIX
```

```typescript
Path\To\ClassName::PREFIX_*_SUFFIX
```

```typescript
Path\To\ClassName::*
```

**Counter-example.** A global mask must have at least one segment of a name,
whether it stands in front of a wildcard or behind it; a lone asterisk, which
would match every constant there is, is not a type.

```typescript counter-example
*
```

```
ParseException: Unexpected "*", expected a type name, an identifier, "true", "false" and other
```

**Counter-example.** Two wildcards in a row are not a mask.

```typescript counter-example
Path\To\ClassName::PREFIX_**
```

```
ParseException: Unexpected "*"
```

**Counter-example.** A mask is a single token, and no gap stands inside it.

```typescript counter-example
JSON_ *
```

```
ParseException: Unexpected "*", expected a type name, an identifier, "true", "false" and other
```
