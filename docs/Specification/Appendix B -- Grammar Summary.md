# B. Appendix: Grammar Summary

This appendix consolidates every grammar production defined in this
specification. Lexical productions (double colon `::`) appear first, followed
by syntactic productions (single colon `:`). It is provided for convenience
and is non-normative; in the event of any discrepancy between this appendix
and the body of this specification, the body governs.

**Source Text**

SourceCharacter :: "Any Unicode code point"

Letter :: one of

- `A` `B` `C` `D` `E` `F` `G` `H` `I` `J` `K` `L` `M`
- `N` `O` `P` `Q` `R` `S` `T` `U` `V` `W` `X` `Y` `Z`
- `a` `b` `c` `d` `e` `f` `g` `h` `i` `j` `k` `l` `m`
- `n` `o` `p` `q` `r` `s` `t` `u` `v` `w` `x` `y` `z`
- "Any byte from 0x80 to 0xFF"

Digit :: one of `0` `1` `2` `3` `4` `5` `6` `7` `8` `9`

**Ignored Tokens**

Ignored ::

- Whitespace
- Comment

Whitespace :: one of

- "Horizontal Tab (U+0009)"
- "Line Feed (U+000A)"
- "Vertical Tab (U+000B)"
- "Form Feed (U+000C)"
- "Carriage Return (U+000D)"
- "Space (U+0020)"

LineTerminator :: one of "New Line (U+000A)" "Carriage Return (U+000D)"

Comment ::

- LineComment
- BlockComment

LineComment :: LineCommentStart CommentChar\*

LineCommentStart :: one of `//` `#`

CommentChar :: SourceCharacter but not LineTerminator

BlockComment :: `/*` BlockCommentChar\* `*/`

BlockCommentChar :: SourceCharacter but not `*/`

**Lexical Tokens**

Token ::

- Punctuator
- NamespacePrefix
- IdentifierMask
- VirtualName
- NameToken
- ReservedWord
- IsNotOperator
- Variable
- ThisVariable
- IntLiteral
- FloatLiteral
- StringLiteral
- BoolLiteral
- NullLiteral

Punctuator :: one of

- `?` `|` `&` `*` `,` `:` `=`
- `(` `)` `[` `]` `{` `}`
- `<` `>`
- `::` `...`

NameToken :: NameStart NameContinue\* [lookahead != NameContinue]

NameStart ::

- Letter
- `_`

NameContinue ::

- Letter
- Digit
- `_`

VirtualName :: NameStart NameContinue\* (`-` NameContinue+)+

NamespacePrefix ::

- `\` (NameToken `\`)\* [lookahead {NameStart, `*`}]
- (NameToken `\`)+ [lookahead {NameStart, `*`}]

IdentifierMask :: MaskHead MaskRest

MaskHead ::

- NameStart NameContinue\* `*`
- `*` [lookahead NameContinue]

MaskRest :: (NameContinue+ `*`)\* NameContinue\*

ReservedWord :: one of `true` `false` `null` `is` `not` `of` `as` `super` `covariant` `contravariant` `invariant`

IsNotOperator :: `is` Trivia+ `not`

Trivia ::

- Whitespace
- Comment

Variable :: `$` NameStart NameContinue\*

ThisVariable :: `$this` [lookahead != NameContinue]

**Literal Tokens**

BoolLiteral :: one of `true` `false` [lookahead != NameContinue] [lookahead != `-`] [lookahead != `\`]

NullLiteral :: `null` [lookahead != NameContinue] [lookahead != `-`] [lookahead != `\`]

NegativeSign :: `-`

Sign :: SignCharacter Whitespace\*

SignCharacter :: one of `-` `+`

DigitSeparator :: `_`

Digits :: Digit (DigitSeparator? Digit)\*

IntLiteral ::

- BinaryIntLiteral
- OctalIntLiteral
- HexIntLiteral
- DecimalIntLiteral

DecimalIntLiteral ::

- Sign? NonZeroDigit (DigitSeparator? Digit)\*
- Sign? `0`

NonZeroDigit :: one of `1` `2` `3` `4` `5` `6` `7` `8` `9`

BinaryIntLiteral :: Sign? `0` BinaryIndicator BinaryDigits

BinaryIndicator :: one of `b` `B`

BinaryDigits :: BinaryDigit (DigitSeparator? BinaryDigit)\*

BinaryDigit :: one of `0` `1`

OctalIntLiteral ::

- Sign? `0` OctalIndicator OctalDigits
- Sign? `0` (DigitSeparator? OctalDigit)+

OctalIndicator :: one of `o` `O`

OctalDigits :: OctalDigit (DigitSeparator? OctalDigit)\*

OctalDigit :: one of `0` `1` `2` `3` `4` `5` `6` `7`

HexIntLiteral :: Sign? `0` HexIndicator HexDigits

HexIndicator :: one of `x` `X`

HexDigits :: HexDigit (DigitSeparator? HexDigit)\*

HexDigit :: one of

- `0` `1` `2` `3` `4` `5` `6` `7` `8` `9`
- `a` `b` `c` `d` `e` `f`
- `A` `B` `C` `D` `E` `F`

FloatLiteral ::

- LeadingFloatLiteral
- TrailingFloatLiteral
- ExponentFloatLiteral

LeadingFloatLiteral :: Sign? Digits `.` Digits? ExponentPart?

TrailingFloatLiteral :: Sign? `.` Digits ExponentPart?

ExponentFloatLiteral :: Sign? Digits ExponentPart

ExponentPart :: ExponentIndicator SignCharacter? Digits

ExponentIndicator :: one of `e` `E`

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
- `\` SourceCharacter

EscapeSequence ::

- SimpleEscape
- HexEscape
- OctalEscape
- UnicodeEscape

SimpleEscape :: one of `n` `r` `t` `v` `e` `f` `$` `"` `\`

HexEscape :: HexIndicator HexDigit HexDigit?

OctalEscape :: OctalDigit OctalDigit? OctalDigit?

UnicodeEscape :: `u` `{` HexDigit+ `}`

**Types**

Document : Type

Type : Expression

Expression : ConditionalType

ConditionalType :

- LogicalType ConditionalSuffix?
- Variable ConditionalSuffix

ConditionalSuffix : ConditionalOperator ConditionalOperand `?` Type `:` Type

ConditionalOperand :

- LogicalType
- Variable

ConditionalOperator : one of `is` `is not`

**Logical Types**

LogicalType : UnionType

UnionType : IntersectionType (`|` IntersectionType)\*

IntersectionType : UnaryType (`&` UnaryType)\*

UnaryType : NullableType

NullableType : `?`? PostfixType

**List and Offset Access Types**

PostfixType : PrimaryType TypeSuffix\*

TypeSuffix :

- ListSuffix
- OffsetSuffix

ListSuffix : `[` `]`

OffsetSuffix : `[` Type `]`

**Primary Types**

PrimaryType :

- `(` Type `)`
- ThisVariable
- LiteralType
- CallableType
- NamedType

**Names**

Name :

- NamespacePrefix Identifier
- VirtualName
- Identifier

Identifier :

- NameToken
- ReservedWord

**Named and Generic Types**

NamedType : Name (TemplateArguments | ShapeFields)?

TemplateArguments : `<` TemplateArgument (`,` TemplateArgument)\* `,`? `>`

TemplateArgument :

- Wildcard
- Variance? Type

Variance : one of `covariant` `contravariant` `invariant`

Wildcard : `*`

**Literal and Constant Types**

LiteralType :

- BoolLiteral
- NullLiteral
- IntLiteral
- FloatLiteral
- StringLiteral
- ClassConstant
- ConstantMask

ClassConstant : Name `::` Identifier

ConstantMask :

- GlobalConstantMask
- ClassConstantMask

GlobalConstantMask :

- IdentifierMask
- NamespacePrefix IdentifierMask
- NamespacePrefix Wildcard

ClassConstantMask : Name `::` (IdentifierMask | Wildcard)

**Shape Types**

ShapeFields : `{` (ShapeBody `,`?)? `}`

ShapeBody :

- ShapeFieldList (`,` UnsealedShape)?
- UnsealedShape

ShapeFieldList : ShapeField (`,` ShapeField)\*

ShapeField : ExplicitField | ImplicitField

ExplicitField : ShapeKey `?`? `:` ShapeValue

ImplicitField : ShapeValue

ShapeKey :

- ClassConstant
- Name
- IntLiteral
- StringLiteral

ShapeValue : Type

UnsealedShape : `...` TemplateArguments?

**Callable Types**

CallableType : Name TemplateParameters? `(` CallableParameters? `)` CallableReturnType?

TemplateParameters : `<` TemplateParameter (`,` TemplateParameter)\* `,`? `>`

TemplateParameter : Variance? Identifier UpperBound? LowerBound? TemplateDefault?

UpperBound : UpperBoundOperator Type

UpperBoundOperator : one of `of` `as`

LowerBound : `super` Type

TemplateDefault : `=` Type

CallableParameters : CallableParameter (`,` CallableParameter)\* `,`?

CallableReturnType : `:` UnaryType

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
