# Lexical Grammar

A TypeLang document is defined as a syntactic grammar whose terminal symbols are
_tokens_ (indivisible lexical units). These tokens are themselves defined by a
lexical grammar which matches patterns of source characters. In this document,
syntactic grammar productions are distinguished with a single colon `:`, while
lexical grammar productions are distinguished with a double colon `::`.

The source text of a TypeLang document must be a sequence of {SourceCharacter}.
That character sequence is first scanned, from left to right, into a sequence of
{Token} and {Ignored} lexical units. The resulting token sequence, omitting every
{Ignored} unit, must then be described by a single {Type} syntactic grammar (see
[Types](#sec-Types)).

Note: See [Appendix A](#sec-Appendix-Notation-Conventions) for more information
about the lexical and syntactic grammar and the other notational conventions used
throughout this document.

**Lexical Analysis**

The source text is scanned by repeatedly taking the next longest possible
sequence of code points allowed by the lexical grammar productions as the next
token (a "[maximal munch](https://en.wikipedia.org/wiki/Maximal_munch)" longest
match). Where this rule alone would be ambiguous, the production order given in
this section is authoritative: an earlier production is preferred over a later
one.

## Source Text

SourceCharacter :: "Any Unicode code point"

TypeLang documents are interpreted from a source text, which is a sequence of
{SourceCharacter}. Any Unicode code point may appear in the source text.

For the purposes of the lexical grammar, a _letter_ is any of the ASCII
characters `a` through `z` and `A` through `Z`, together with every byte in the
range U+0080 through U+00FF. This range allows non-ASCII bytes (for example those
of a UTF-8 encoded identifier) to appear within names.

Letter :: one of

- `A` `B` `C` `D` `E` `F` `G` `H` `I` `J` `K` `L` `M`
- `N` `O` `P` `Q` `R` `S` `T` `U` `V` `W` `X` `Y` `Z`
- `a` `b` `c` `d` `e` `f` `g` `h` `i` `j` `k` `l` `m`
- `n` `o` `p` `q` `r` `s` `t` `u` `v` `w` `x` `y` `z`
- "Any byte from U+0080 to U+00FF"

Digit :: one of `0` `1` `2` `3` `4` `5` `6` `7` `8` `9`

## Ignored Tokens

Ignored ::

- Whitespace
- Comment

{Ignored} tokens are used to improve readability and to separate lexical tokens,
but are otherwise insignificant. Any amount of {Ignored} may appear before and
after every lexical {Token}.

Note: Although {Ignored} between two tokens is never significant on its own, the
_presence_ of separating whitespace is what distinguishes a {Name} that is
immediately followed by another token from a {Name} that introduces a template
argument hint (see [Template Argument Hints](#sec-Template-Argument-Hints)). This
is the only place in the grammar where the existence of separating whitespace is
observable.

### White Space

Whitespace :: "Any Unicode whitespace character"

Whitespace separates tokens and improves the legibility of the source text. It
includes, at minimum, the space (U+0020), horizontal tab (U+0009), line feed
(U+000A) and carriage return (U+000D) characters. TypeLang does not distinguish
between horizontal whitespace and line terminators, except within the multi-word
`is not` operator (see [Conditional Types](#sec-Conditional-Types)).

### Comments

Comment ::

- LineComment
- BlockComment

LineComment :: LineCommentStart CommentChar\*

LineCommentStart :: one of `//` `#`

CommentChar :: SourceCharacter but not LineTerminator

BlockComment :: `/*` BlockCommentChar\* `*/`

BlockCommentChar :: SourceCharacter but not `*/`

LineTerminator :: one of "New Line (U+000A)" "Carriage Return (U+000D)"

TypeLang source documents may contain comments. A _line comment_ begins with
either the `//` or `#` marker and continues up to, but not including, the next
{LineTerminator} (or the end of the source). A _block comment_ begins with `/*`
and continues up to and including the next `*/`.

Comments are {Ignored} and have no significance to the meaning of a document.

Note: The `#` line comment marker and the `#[` attribute marker share a leading
character. Because lexical analysis prefers the longest match, the sequence `#[`
is always scanned as the start of an attribute (see
[Attributes](#sec-Attributes)) rather than as a comment.

## Lexical Tokens

Token ::

- Punctuator
- Name
- Variable
- IntLiteral
- FloatLiteral
- StringLiteral
- BoolLiteral
- NullLiteral

A TypeLang document is composed of several kinds of indivisible lexical tokens,
defined here by patterns of source characters. Lexical tokens may be separated by
{Ignored} tokens, and are used as terminal symbols by the syntactic grammar.

### Punctuators

Punctuator :: one of

- `?` `|` `&` `*` `,` `:` `;` `=` `!`
- `(` `)` `[` `]` `{` `}`
- `<` `>` `<=` `>=`
- `::` `\` `...` `#[`

TypeLang documents include punctuation in order to describe structure. Several
punctuators share a leading character (for example `<`, `<=`; `:`, `::`; `.` in
`...`; `#` in `#[`); in every such case the longest matching punctuator is taken.

### Names

Name :: NameStart NameContinue\* [lookahead != NameContinue]

NameStart ::

- Letter
- `_`

NameContinue ::

- Letter
- Digit
- `_`
- `-`

A {Name} must begin with a {Letter} or an underscore (`_`) and may continue with
any {Letter}, {Digit}, underscore, or dash (`-`). The only difference from the
[PHP identifier grammar](https://www.php.net/manual/en/language.variables.basics.php)
is that the dash character is additionally permitted in any non-leading position.

A {Name} is always the longest possible valid sequence; it must not be followed
by a {NameContinue}.

```typescript
ExampleTypeName
```

Dashes are accepted in any non-leading position, which is what makes built-in
type names such as `non-empty-string` and `array-key` expressible:

```typescript
non-empty-string
```

The reserved words `true`, `false` and `null` are also valid as a fragment of a
{Name}, provided the {Name} is not _equal_ to one of those reserved words on its
own (see [Reserved Words](#sec-Reserved-Words)):

```typescript
true-type
```

**Counter-example.** A {Name} cannot begin with a {Digit} or a dash.

```typescript counter-example
42type
```

```
ParseException: Syntax error, unexpected "type"
```

### Reserved Words

ReservedWord :: one of `true` `false` `null` `is`

The words `true`, `false` and `null` are _literal_ keywords (see
[Literal Tokens](#sec-Literal-Tokens)), and `is` is the conditional operator
keyword (see [Conditional Types](#sec-Conditional-Types)). All four are matched
case-insensitively and only when they are not immediately followed by a
{NameContinue} character.

When the source contains one of these words standing alone (in a position where a
type is expected), it is scanned as the corresponding literal or operator token
and **not** as a {Name}. Consequently a bare reserved word cannot be used as a
type name:

```typescript counter-example
TrUe
```

A reserved word may, however, appear as an {Identifier} _inside_ a qualified
{Name} — that is, when it is preceded by a namespace separator or another
identifier (see [Names and Namespaces](#sec-Names-and-Namespaces)). For example
`\true` references a type literally named `true`, whereas the bare `true` is the
boolean literal.

### Variable

Variable :: VariableStart `$` NameStart NameContinue\*

ThisVariable :: `$this` [lookahead != NameContinue]

A {Variable} token begins with a dollar sign (`$`) followed by a {Name}-like
sequence. Variables are used to name callable parameters (see
[Callable Types](#sec-Callable-Types)) and as operands in conditional types (see
[Conditional Types](#sec-Conditional-Types)).

The special variable `$this` is recognised as a distinct token and additionally
denotes the current object type when used as a primary type (see
[Primary Types](#sec-Primary-Types)).

Note: The leading `$` distinguishes variables from names at the lexical level, so
no reserved-word restriction applies to the part following the `$`.

## Literal Tokens

A _literal_ denotes a single, specific PHP value. The lexical grammar recognises
boolean, null, integer, floating-point and string literals.

### Boolean

BoolLiteral :: one of `true` `false` [lookahead != NameContinue]

The case-insensitive words `true` and `false` denote the two values of the PHP
`bool` type. Case is not significant: `true`, `TRUE` and `TruE` all denote the
same value.

### Null

NullLiteral :: `null` [lookahead != NameContinue]

The case-insensitive word `null` denotes the PHP `null` value. As with booleans,
case is not significant.

### Integer

IntLiteral ::

- BinaryIntLiteral
- OctalIntLiteral
- HexIntLiteral
- DecimalIntLiteral

NegativeSign :: `-`

DigitSeparator :: `_`

An integer literal denotes a value of the PHP `int` type. Binary, octal, decimal
and hexadecimal radixes are supported, each optionally prefixed with a
{NegativeSign}. Within the digits of any integer literal, underscores
({DigitSeparator}) may appear freely as visual separators and carry no meaning.

**Decimal.**

DecimalIntLiteral :: NegativeSign? Digit (Digit | DigitSeparator)\*

```typescript
1_000_000
```

**Binary.** Prefixed with `0b` or `0B`; digits are `0` and `1` only.

BinaryIntLiteral :: NegativeSign? `0` BinaryIndicator BinaryDigit (BinaryDigit | DigitSeparator)\*

BinaryIndicator :: one of `b` `B`

BinaryDigit :: one of `0` `1`

```typescript
0b1010_1101
```

**Octal.** Prefixed with `0o` or `0O`; digits are `0` through `7` only.

OctalIntLiteral :: NegativeSign? `0` OctalIndicator OctalDigit (OctalDigit | DigitSeparator)\*

OctalIndicator :: one of `o` `O`

OctalDigit :: one of `0` `1` `2` `3` `4` `5` `6` `7`

```typescript
0o42
```

**Hexadecimal.** Prefixed with `0x` or `0X`; digits are `0` through `9` and `a`
through `f` in either case.

HexIntLiteral :: NegativeSign? `0` HexIndicator HexDigit (HexDigit | DigitSeparator)\*

HexIndicator :: one of `x` `X`

HexDigit :: one of

- `0` `1` `2` `3` `4` `5` `6` `7` `8` `9`
- `a` `b` `c` `d` `e` `f`
- `A` `B` `C` `D` `E` `F`

```typescript
0xDEAD_BEEF
```

**Counter-example.** A radix may only contain digits valid for that radix.

```typescript counter-example
0b101042
```

```
ParseException: Syntax error, unexpected "42"
```

**Static Semantics.**

A {DecimalIntLiteral} whose first digit is `0` and which is longer than a single
character is interpreted in the _legacy octal_ radix (base 8), for compatibility
with historical PHP source. Thus `042` denotes the value 34.

A conforming implementation stores the original (raw) lexeme of every integer
literal. When the denoted value exceeds the platform integer range, the numeric
value is clamped to the nearest representable bound (`PHP_INT_MAX` or
`PHP_INT_MIN`) while the raw lexeme is preserved unchanged.

### Float

FloatLiteral ::

- LeadingFloatLiteral
- TrailingFloatLiteral
- ExponentFloatLiteral

LeadingFloatLiteral :: NegativeSign? Digit+ `.` Digit\* ExponentPart?

TrailingFloatLiteral :: NegativeSign? Digit\* `.` Digit+ ExponentPart?

ExponentFloatLiteral :: NegativeSign? Digit+ ExponentPart

ExponentPart :: ExponentIndicator NegativeSign? Digit+

ExponentIndicator :: one of `e` `E`

A floating-point literal denotes a value of the PHP `float` type. It must contain
either a decimal point or an exponent (or both), and may be prefixed with a
{NegativeSign}.

Either the leading run of digits (before the decimal point) or the trailing run
(after it) may be omitted, but not both:

```typescript
0.9
```

```typescript
.9
```

```typescript
1.
```

Scientific notation uses the case-insensitive `e` indicator followed by an
optionally negative decimal exponent:

```typescript
10e-2
```

**Counter-example.** A lone decimal point is not a valid float.

```typescript counter-example
.
```

```
ParseException: Syntax error, unexpected "."
```

Note: Because the lexer takes the longest match, the source `1.23` is a single
{FloatLiteral} and never the two tokens `1.2` and `3`.

### String

StringLiteral ::

- SingleQuotedString
- DoubleQuotedString

SingleQuotedString :: `'` SingleStringChar\* `'`

SingleStringChar ::

- SourceCharacter but not `'` or `\`
- `\` SourceCharacter

DoubleQuotedString :: `"` DoubleStringChar\* `"`

DoubleStringChar ::

- SourceCharacter but not `"` or `\`
- `\` EscapeSequence

A string literal denotes a value of the PHP `string` type. Strings are delimited
by single (`'`) or double (`"`) quotes. A delimiter, and the backslash, may be
escaped by a preceding backslash.

```typescript
'I am a single-quoted string with an escaped \' quote'
```

```typescript
"I am a double-quoted string with an escaped \" quote"
```

The two forms differ in how they treat backslash escapes. A single-quoted string
is interpreted verbatim: the only meaningful escapes are `\'` and `\\`, and every
other backslash is preserved literally. A double-quoted string interprets the
full set of {EscapeSequence} productions described below.

**Escape Sequences.**

EscapeSequence ::

- SimpleEscape
- HexEscape
- UnicodeEscape

SimpleEscape :: one of `n` `r` `t` `v` `e` `f` `$` `"` `\`

HexEscape :: `x` HexDigit HexDigit?

UnicodeEscape :: `u` `{` HexDigit+ `}`

Inside a double-quoted string, the following simple escape sequences are
recognised, mirroring the
[PHP string syntax](https://www.php.net/manual/en/language.types.string.php):

| Sequence | Produces                  |
| -------- | ------------------------- |
| `\n`     | line feed (U+000A)        |
| `\r`     | carriage return (U+000D)  |
| `\t`     | horizontal tab (U+0009)   |
| `\v`     | vertical tab (U+000B)     |
| `\e`     | escape (U+001B)           |
| `\f`     | form feed (U+000C)        |
| `\$`     | dollar sign (U+0024)      |
| `\"`     | double quote (U+0022)     |
| `\\`     | reverse solidus (U+005C)  |

A {HexEscape} (`\x` followed by one or two hexadecimal digits) denotes the
character with that byte value; for example `"\x41"` denotes `"A"`.

A {UnicodeEscape} (`\u{...}`) denotes the Unicode code point named by the
hexadecimal value within the braces, emitted as its UTF-8 representation; the
braces are required. For example `"\u{1F60A}"` denotes the emoji 😊.

Note: None of the escape, hexadecimal, or Unicode sequences are interpreted
inside a single-quoted string; there, `\x41`, `\u{1F60A}` and `\n` denote those
exact characters verbatim.
