# Lexical Grammar

A TypeLang document is defined by a syntactic grammar whose terminal symbols
are _tokens_ (indivisible lexical units). Tokens are themselves defined by a
lexical grammar that matches patterns of source characters. Throughout this
document, syntactic grammar productions are distinguished by a single colon
`:`, while lexical grammar productions are distinguished by a double colon
`::` (see [Appendix A](#sec-Appendix-Notation-Conventions)).

The source text of a TypeLang document MUST be a sequence of
{SourceCharacter}. That sequence is first scanned, from left to right, into a
sequence of {Token} and {Ignored} lexical units. The resulting token
sequence, once every {Ignored} unit is discarded, MUST then be described in
full by a single {Type} syntactic production (see [Types](#sec-Types)).

Note: See [Appendix A](#sec-Appendix-Notation-Conventions) for further
information about the lexical and syntactic grammar and the other notational
conventions used throughout this document.

**Lexical Analysis**

The source text is scanned by repeatedly taking the next longest possible
sequence of code points permitted by the lexical grammar productions as the
next token (a "[maximal munch](https://en.wikipedia.org/wiki/Maximal_munch)"
longest-match discipline). Where this rule alone would be ambiguous — that is,
where two or more productions match a sequence of equal length starting at the
same position — the production order given in this section SHALL be
authoritative: an earlier production is preferred over a later one.

## Source Text

SourceCharacter :: "Any Unicode code point"

TypeLang documents are interpreted from a source text, which is a sequence of
{SourceCharacter}. Any Unicode code point may appear in the source text.

Note: This specification describes the lexical grammar in terms of Unicode
code points. The reference implementation, in common with most PHP source
tooling, instead operates on a byte string that is assumed (but not required)
to be UTF-8 encoded. Within the ASCII range (U+0000 through U+007F), a byte
and the code point it encodes coincide, and this specification's rules apply
identically whether stated in terms of bytes or of code points. For the
non-ASCII range, this specification treats every byte from U+0080 through
U+00FF individually as a {Letter} (see below) without decoding it; this
happens to accept identifiers containing correctly encoded multi-byte UTF-8
sequences, but it does not itself validate that a source text is
well-formed UTF-8.

For the purposes of the lexical grammar, a _letter_ is any of the ASCII
characters `a` through `z` and `A` through `Z`, together with every byte in
the range U+0080 through U+00FF.

Letter :: one of

- `A` `B` `C` `D` `E` `F` `G` `H` `I` `J` `K` `L` `M`
- `N` `O` `P` `Q` `R` `S` `T` `U` `V` `W` `X` `Y` `Z`
- `a` `b` `c` `d` `e` `f` `g` `h` `i` `j` `k` `l` `m`
- `n` `o` `p` `q` `r` `s` `t` `u` `v` `w` `x` `y` `z`
- "Any byte from 0x80 to 0xFF"

Digit :: one of `0` `1` `2` `3` `4` `5` `6` `7` `8` `9`

## Ignored Tokens

Ignored ::

- Whitespace
- Comment

{Ignored} tokens improve readability and separate lexical tokens, but are
otherwise insignificant. Any amount of {Ignored} MAY appear before and after
every lexical {Token}.

Note: Although {Ignored} between two tokens is never itself significant, the
_presence_ of {Whitespace} standing directly behind a {NameToken} is what
distinguishes a {NameToken} that is immediately followed by another token
from a {NameToken} that introduces a template argument hint (see
[Template Argument Hints](#sec-Template-Argument-Hints)). This is the only
place in the grammar where {Ignored} is observable to the syntactic grammar,
and the one place a {Comment} does not stand for the whitespace it is written
among: a comment MAY follow that whitespace, but MUST NOT stand in its stead.

### White Space

Whitespace :: one of

- "Horizontal Tab (U+0009)"
- "Line Feed (U+000A)"
- "Vertical Tab (U+000B)"
- "Form Feed (U+000C)"
- "Carriage Return (U+000D)"
- "Space (U+0020)"

Whitespace separates tokens and improves the legibility of the source text.
TypeLang does not distinguish between horizontal whitespace and line
terminators, and a {Comment} stands wherever whitespace stands, save for the
one place the presence of the whitespace itself is read (see
[Ignored Tokens](#sec-Ignored-Tokens)).

Note: The six characters above are the whole of it. A source text is read as
a sequence of bytes (see [Source Text](#sec-Source-Text)), so a character that
parts words in a script of its own — a no-break space (U+00A0), say — is no
whitespace here: every byte it is written of falls in the range a {Letter}
covers, and it is read as a part of the name it stands in.

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

A TypeLang source document MAY contain comments. A _line comment_ begins with
either the `//` or `#` marker and continues up to, but not including, the next
{LineTerminator} (or the end of the source, whichever occurs first). A _block
comment_ begins with `/*` and continues up to and including the next `*/`.

Comments are {Ignored} and have no bearing on the meaning of a document.

## Lexical Tokens

Token ::

- Punctuator
- NameToken
- Variable
- IntLiteral
- FloatLiteral
- StringLiteral
- BoolLiteral
- NullLiteral

A TypeLang document is composed of several kinds of indivisible lexical
tokens, defined here by patterns of source characters. Lexical tokens MAY be
separated by {Ignored} tokens and are used as the terminal symbols of the
syntactic grammar.

### Punctuators

Punctuator :: one of

- `?` `|` `&` `*` `,` `:` `=`
- `(` `)` `[` `]` `{` `}`
- `<` `>`
- `::` `\` `...`

TypeLang documents use punctuation to describe structure. Several punctuators
share a leading character (for example, `:` and `::`; `.` in `...`); in every
such case, the longest matching punctuator is taken.

### Names

NameToken :: NameStart NameContinue\* [lookahead != NameContinue]

NameStart ::

- Letter
- `_`

NameContinue ::

- Letter
- Digit
- `_`
- `-`

A {NameToken} is the raw lexical unit that is later assembled, together with
the namespace separator, into the syntactic {Name} production (see
[Names and Namespaces](#sec-Names-and-Namespaces)); the two are distinct
non-terminals of, respectively, the lexical and the syntactic grammar. A
{NameToken} MUST begin with a {Letter} or an underscore (`_`) and MAY continue
with any {Letter}, {Digit}, underscore, or dash (`-`). The only difference from
the [PHP identifier grammar](https://www.php.net/manual/en/language.variables.basics.php)
is that the dash character is additionally permitted in any non-leading
position.

A source text is read as a sequence of bytes rather than of codepoints, and
every byte of a character outside of ASCII falls in the 0x80 to 0xFF range a
{Letter} covers. A name MAY therefore be written in any script.

```typescript
Проект\Тип
```

A {NameToken} is always the longest possible valid sequence; it MUST NOT be
followed by a {NameContinue} character.

```typescript
ExampleTypeName
```

Dashes are accepted in any non-leading position, which is what makes built-in
type names such as `non-empty-string` and `array-key` expressible:

```typescript
non-empty-string
```

The reserved words `true`, `false` and `null` are also valid as a fragment of
a {NameToken}, provided the {NameToken} is not _equal_ to one of those
reserved words on its own (see [Reserved Words](#sec-Reserved-Words)):

```typescript
true-type
```

**Counter-example.** A {NameToken} cannot begin with a {Digit} or a dash.

```typescript counter-example
42type
```

```
ParseException: Syntax error, unexpected "type"
```

### Reserved Words

ReservedWord :: one of `true` `false` `null` `is` `not`

The words `true`, `false` and `null` are _literal_ keywords (see
[Literal Tokens](#sec-Literal-Tokens)), and `is` and `not` are the
conditional operator keywords (see
[Conditional Types](#sec-Conditional-Types)). Each is matched only when not
immediately followed by a {NameContinue} character. The three literal
keywords are matched case-insensitively, while `is` and `not` are matched
the one way they are spelled.

Wherever the source contains one of these words, it is scanned as the
corresponding literal or operator token and MUST NOT be scanned as a
{NameToken}. It remains an {Identifier} all the same (see
[Names and Namespaces](#sec-Names-and-Namespaces)), so a reserved word is
shadowed rather than forbidden: it stands as a name wherever the token it is
scanned as denotes no type of its own.

A literal keyword standing alone, in a position where a type is expected, is
read as the {LiteralType} it denotes and not as the {NamedType} it would
otherwise be (see [Primary Types](#sec-Primary-Types)):

```typescript
TrUe
```

Behind a namespace separator or another segment, where no literal is
expected, the same word is a name segment. This makes `\true` a reference to
a type literally named `true`, distinct from the `true` literal:

```typescript
\true
```

The conditional operator keywords denote no type of their own, so nothing
shadows them, and a bare `is` or `not` is an ordinary named type:

```typescript
not
```

### Variable

Variable :: `$` NameStart VariableContinue\*

VariableContinue :: NameContinue but not `-`

ThisVariable :: `$this` [lookahead != VariableContinue]

A {Variable} token begins with a dollar sign (`$`) followed by a sequence
matching the body of a {NameToken}, save for the dash a variable cannot
carry. Variables are used to name callable parameters (see [Callable Types](#sec-Callable-Types)) and as operands in
conditional types (see [Conditional Types](#sec-Conditional-Types)).

The special variable `$this` is recognised as a distinct token and
additionally denotes the current object type when used as a primary type (see
[Primary Types](#sec-Primary-Types)).

Note: The leading `$` distinguishes variables from names at the lexical
level, so no reserved-word restriction applies to the part of a {Variable}
token that follows the `$`.

## Literal Tokens

A _literal_ denotes a single, specific PHP value. The lexical grammar
recognises boolean, null, integer, floating-point and string literals.

### Boolean

BoolLiteral :: one of `true` `false` [lookahead != NameContinue]

The case-insensitive words `true` and `false` denote the two values of the PHP
`bool` type. Case is not significant: `true`, `TRUE` and `TruE` all denote the
same value.

### Null

NullLiteral :: `null` [lookahead != NameContinue]

The case-insensitive word `null` denotes the PHP `null` value. As with
booleans, case is not significant.

### Integer

IntLiteral ::

- BinaryIntLiteral
- OctalIntLiteral
- HexIntLiteral
- DecimalIntLiteral

NegativeSign :: `-`

Sign :: one of `-` `+`

DigitSeparator :: `_`

An integer literal denotes a value of the PHP `int` type. Binary, octal,
decimal and hexadecimal radixes are supported, each optionally prefixed with a
{Sign}. A {NegativeSign} makes the denoted value negative; a leading `+` denotes
the same value the absence of a {Sign} does. Underscores ({DigitSeparator}) MAY
appear as visual separators; they carry no meaning and do not affect the denoted
value. A {DigitSeparator} MUST stand between two digits, and therefore may
neither lead a run of digits, nor trail one, nor stand beside another
{DigitSeparator}.

Digits :: Digit (DigitSeparator? Digit)\*

**Decimal.** A decimal literal other than a lone `0` MUST NOT begin with a
zero, which is the mark of an {OctalIntLiteral}.

DecimalIntLiteral ::

- Sign? NonZeroDigit (DigitSeparator? Digit)\*
- Sign? `0`

NonZeroDigit :: one of `1` `2` `3` `4` `5` `6` `7` `8` `9`

```typescript
1_000_000
```

**Binary.** Prefixed with `0b` or `0B`; digits are `0` and `1` only.

BinaryIntLiteral :: Sign? `0` BinaryIndicator BinaryDigits

BinaryIndicator :: one of `b` `B`

BinaryDigits :: BinaryDigit (DigitSeparator? BinaryDigit)\*

BinaryDigit :: one of `0` `1`

```typescript
0b1010_1101
```

**Octal.** Prefixed with `0o` or `0O` or, as octal has been written since long
before that prefix, with a leading `0` alone; digits are `0` through `7` only.
A `0123` denotes the same value a `0o123` does.

OctalIntLiteral ::

- Sign? `0` OctalIndicator OctalDigits
- Sign? `0` (DigitSeparator? OctalDigit)+

OctalIndicator :: one of `o` `O`

OctalDigits :: OctalDigit (DigitSeparator? OctalDigit)\*

OctalDigit :: one of `0` `1` `2` `3` `4` `5` `6` `7`

```typescript
0o42
```

```typescript
04_23
```

**Counter-example.** A leading zero makes a literal an octal, so a digit
outside the octal radix may not follow one.

```typescript counter-example
08
```

```
ParseException: Syntax error, unexpected "8"
```

**Hexadecimal.** Prefixed with `0x` or `0X`; digits are `0` through `9` and `a`
through `f`, in either case.

HexIntLiteral :: Sign? `0` HexIndicator HexDigits

HexIndicator :: one of `x` `X`

HexDigits :: HexDigit (DigitSeparator? HexDigit)\*

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

An {OctalIntLiteral} written without an {OctalIndicator} is the _legacy octal_
spelling kept for compatibility with historical PHP source. It is read in the
same radix (base 8) the prefixed spelling is, so `042` denotes the value 34.

A conforming implementation MUST retain the original (raw) lexeme of every
integer literal. When the denoted value exceeds the range representable by the
implementation's platform integer type, the numeric value MUST be clamped to
the nearest representable bound (the platform's `PHP_INT_MAX` or
`PHP_INT_MIN`) while the raw lexeme MUST be preserved unchanged.

### Float

FloatLiteral ::

- LeadingFloatLiteral
- TrailingFloatLiteral
- ExponentFloatLiteral

LeadingFloatLiteral :: Sign? Digits `.` Digits? ExponentPart?

TrailingFloatLiteral :: Sign? `.` Digits ExponentPart?

ExponentFloatLiteral :: Sign? Digits ExponentPart

ExponentPart :: ExponentIndicator Sign? Digits

ExponentIndicator :: one of `e` `E`

A floating-point literal denotes a value of the PHP `float` type. It MUST
contain either a decimal point or an exponent, or both, and MAY be prefixed
with a {Sign}.

Either the leading run of digits (before the decimal point) or the trailing
run (after it) MAY be omitted, but not both:

```typescript
0.9
```

```typescript
.9
```

```typescript
1.
```

Scientific notation uses the case-insensitive `e` indicator followed by a
decimal exponent, which takes a {Sign} of its own:

```typescript
10e-2
```

```typescript
10e+2
```

Every run of digits a float is written of accepts the {DigitSeparator} under
the same rule an integer does, and, unlike a {DecimalIntLiteral}, MAY begin
with a zero:

```typescript
2_3.4_5e-6_7
```

**Counter-example.** A lone decimal point is not a valid float.

```typescript counter-example
.
```

```
ParseException: Syntax error, unexpected "."
```

Note: Because the lexer takes the longest match, the source `1.23` is always a
single {FloatLiteral} and never the two tokens `1.2` and `3`.

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

A string literal denotes a value of the PHP `string` type. Strings are
delimited by single (`'`) or double (`"`) quotes. A delimiter, and the
backslash itself, may be escaped by a preceding backslash.

```typescript
'I am a single-quoted string with an escaped \' quote'
```

```typescript
"I am a double-quoted string with an escaped \" quote"
```

The two forms differ in how they treat backslash escapes. A single-quoted
string MUST be interpreted verbatim: the only meaningful escapes are `\'` and
`\\`, and every other backslash MUST be preserved literally. A double-quoted
string MUST interpret the full set of {EscapeSequence} productions described
below.

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

| Sequence | Produces                 |
| -------- | ------------------------ |
| `\n`     | line feed (U+000A)       |
| `\r`     | carriage return (U+000D) |
| `\t`     | horizontal tab (U+0009)  |
| `\v`     | vertical tab (U+000B)    |
| `\e`     | escape (U+001B)          |
| `\f`     | form feed (U+000C)       |
| `\$`     | dollar sign (U+0024)     |
| `\"`     | double quote (U+0022)    |
| `\\`     | reverse solidus (U+005C) |

A {HexEscape} (`\x` followed by one or two hexadecimal digits) denotes the
character with that byte value; for example, `"\x41"` denotes `"A"`.

A {UnicodeEscape} (`\u{...}`) denotes the Unicode code point named by the
hexadecimal value within the braces, emitted as its UTF-8 representation; the
braces are REQUIRED. For example, `"\u{E9}"` denotes `"é"` (LATIN SMALL LETTER
E WITH ACUTE, U+00E9).

Note: None of the escape, hexadecimal, or Unicode sequences are interpreted
inside a single-quoted string; there, `\x41`, `\u{E9}` and `\n` each denote
those exact source characters, verbatim.
