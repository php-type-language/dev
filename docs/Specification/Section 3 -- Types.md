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

- Type
- Variable

ConditionalOperator : one of `is` `is not` `>=` `<=` `<` `>`

A _conditional type_ (also called a ternary type) selects between two types
based on a comparison between a _subject_ and a _comparand_. It is written as
the subject, an operator, the comparand, a `?`, the type chosen when the
condition holds, a `:`, and the type chosen otherwise.

```typescript
T is string ? int : bool
```

The negative-equality operator is spelled `is not`. At the lexical level, the
two words MUST be separated only by horizontal whitespace and are scanned as a
single token; no line terminator may appear between them.

```typescript
T is not string ? int : bool
```

Either the subject or the comparand MAY be a {Variable} instead of a {Type},
including in "Yoda" order, where the variable appears on the right:

```typescript
$value is array ? non-empty-array : null
```

```typescript
array is $value ? non-empty-array : null
```

In addition to `is` and `is not`, the relational operators `<`, `>`, `<=` and
`>=` MAY be used as the condition operator. This is an extension beyond what
PHPStan and Psalm accept; see
[Relationship to Other Tools](#sec-Relationship-to-Other-Tools).

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

UnionType : IntersectionType (`|` UnionType)?

A _union type_ is a sequence of two or more member types separated by the pipe
character (`|`). It denotes a value that satisfies at least one of its
members.

```typescript
A | B | C
```

Note: A {UnionType} with a single member denotes that member directly; no
union node is produced unless at least two members are present.

### Intersection Types

IntersectionType : UnaryType (`&` IntersectionType)?

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
ParseException: Syntax error, unexpected "?"
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

**Counter-example.** The legacy list syntax does not accept a key type between
the brackets; for that, use [offset access](#sec-Offset-Access) or the modern
`array<…>` generic.

```typescript counter-example
User[int]
```

This document, however, parses `User[int]` successfully — as an
_offset access_ type (see below) rather than a list — because `int` is a
valid {Type}. It is the absence of a type between the brackets that selects
the list interpretation.

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
ParseException: Syntax error, unexpected "["
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
[Callable Types](#sec-Callable-Types)), or a named type. Per the ordered-choice
discipline that governs every alternation in this grammar (see
[Grammar Notation](#sec-Grammar-Notation)), these five alternatives are
attempted in the order listed, and the first that matches at the current
position is selected.

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
{ConstantMask} forms of {LiteralType} begins with a {Name}. Because a
{LiteralType} is attempted before a {CallableType} or a {NamedType} (see
above), these are distinguished, in effect, by the token that follows the
name: `*` selects a [constant mask](#sec-Constant-Masks); `::` selects a
[class constant](#sec-Class-Constants); `(` selects a
[callable type](#sec-Callable-Types); `<` or `{` selects a
[generic or shape](#sec-Generic-Types) named type; any other following token
leaves a plain named type.

## Names and Namespaces

Name :

- FullyQualifiedName
- RelativeName

FullyQualifiedName : `\` Identifier (`\` Identifier)\*

RelativeName : Identifier (`\` Identifier)\*

Identifier :

- NameToken
- ReservedWord

A {Name} is a sequence of one or more {Identifier} segments joined by the
namespace separator (`\`), mirroring
[PHP namespaces](https://www.php.net/manual/en/language.namespaces.rationale.php).
A _fully-qualified_ name additionally begins with a leading separator.

```typescript
Example\Name
```

```typescript
\Absolute\Type\Name
```

A separator MAY appear at the start of a name or between two segments, but
MUST NOT appear at the end.

**Reserved words within names.** The reserved words `true`, `false`, `null`
and `is` (see [Reserved Words](#sec-Reserved-Words)) MAY appear as an
{Identifier} segment of a {Name}. In isolation, such a word is scanned as a
literal or operator and so cannot stand as a bare type name; but in a
qualified position — following a separator or another segment — it denotes a
name segment. This makes `\null` a reference to a type named `null`, distinct
from the `null` literal.

**Counter-example.** A name cannot end with a separator.

```typescript counter-example
Example\Name\
```

```
ParseException: Syntax error, unexpected end of input
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

TemplateArgument : AttributeGroups? (TemplateArgumentHint | TemplateArgumentType)

TemplateArgumentType : Type

A _generic type_ supplies a named type with one or more _template arguments_,
each of which is itself a {Type}. Arguments are enclosed in angle brackets
(`<` and `>`) and separated by commas. A trailing comma is permitted.

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
ParseException: Syntax error, unexpected ">"
```

Note: Throughout this document, the term _template argument_ refers to a
value supplied at a use site, such as the `int<0, max>` in
`iterable<int<0, max>, …>`. This is distinct from a _template parameter_,
which would be part of a type's (hypothetical) declaration. TypeLang
describes only use sites, and therefore only template arguments.

### Template Argument Hints

TemplateArgumentHint : Identifier Type

A template argument MAY carry a single leading _hint_: an {Identifier} placed
before the argument's type. Hints are used by tooling — for example, to
express [call-site variance](https://phpstan.org/blog/guide-to-call-site-generic-variance#call-site-variance)
with identifiers such as `in`, `out`, `covariant`, or `contravariant`.

```typescript
HashMap<array-key, covariant Request>
```

At the lexical level, the hint and the type it modifies MUST be separated by
whitespace; this separation is what distinguishes a hint from the start of
the argument's own type (see [Ignored Tokens](#sec-Ignored-Tokens)). Each
argument may carry at most one hint.

**Counter-example.** A hint must be a single valid identifier.

```typescript counter-example
HashMap<array-key, some covariant Request>
```

```
ParseException: Syntax error, unexpected "Request"
```

**Counter-example.** A hint cannot be a reserved word: `is`, `true`, `false`
and `null` are always scanned as the corresponding operator or literal token
(see [Reserved Words](#sec-Reserved-Words)) and never as a hint {Identifier}.

```typescript counter-example
HashMap<is Request>
```

```
ParseException: Syntax error, unexpected "Request"
```

### Template Argument Attributes

Each template argument MAY additionally be prefixed with one or more
[attribute groups](#sec-Attributes), providing metadata for the argument.

```typescript
HashMap<#[name("key")] T, #[name("value")] U>
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
that of a {Name}, constant names share its restrictions: they may not be one
of the bare reserved words `true`, `false` or `null`. Unlike a conventional
type name, however, a constant is conventionally written without a dash.

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
ParseException: Syntax error, unexpected "\"
```

### Constant Masks

ConstantMask :

- Name `*`
- Name `::` Identifier `*`
- Name `::` `*`

A _constant mask_ denotes a family of constants whose names share a common
prefix. A mask MUST terminate with an asterisk (`*`).

A global constant mask matches every global constant beginning with the
prefix:

```typescript
JSON_*
```

A class constant mask matches every constant of a class whose name begins
with the prefix; the prefix MAY be omitted entirely, in which case the mask
matches every constant of the class:

```typescript
Path\To\ClassName::PREFIX_*
```

```typescript
Path\To\ClassName::*
```

**Counter-example.** A global mask must have a prefix; a lone asterisk is not
a type.

```typescript counter-example
*
```

```
ParseException: Syntax error, unexpected "*"
```

**Counter-example.** The asterisk must be the final character of the mask.

```typescript counter-example
Path\To\ClassName::PREFIX_*_SUFFIX
```

```
ParseException: Syntax error, unexpected "_SUFFIX"
```
