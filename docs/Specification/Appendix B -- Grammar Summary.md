# B. Appendix: Grammar Summary

This appendix consolidates every grammar production defined in this specification.
Lexical productions (double colon `::`) appear first, followed by syntactic
productions (single colon `:`).

## Source Text

SourceCharacter :: "Any Unicode code point"

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

Whitespace :: "Any Unicode whitespace character"

LineTerminator :: one of "New Line (U+000A)" "Carriage Return (U+000D)"

Comment ::

- LineComment
- BlockComment

LineComment :: LineCommentStart CommentChar\*

LineCommentStart :: one of `//` `#`

CommentChar :: SourceCharacter but not LineTerminator

BlockComment :: `/*` BlockCommentChar\* `*/`

BlockCommentChar :: SourceCharacter but not `*/`

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

Punctuator :: one of

- `?` `|` `&` `*` `,` `:` `;` `=` `!`
- `(` `)` `[` `]` `{` `}`
- `<` `>` `<=` `>=`
- `::` `\` `...` `#[`

Name :: NameStart NameContinue\* [lookahead != NameContinue]

NameStart ::

- Letter
- `_`

NameContinue ::

- Letter
- Digit
- `_`
- `-`

ReservedWord :: one of `true` `false` `null` `is`

Variable :: `$` NameStart NameContinue\*

ThisVariable :: `$this` [lookahead != NameContinue]

## Literal Tokens

BoolLiteral :: one of `true` `false` [lookahead != NameContinue]

NullLiteral :: `null` [lookahead != NameContinue]

NegativeSign :: `-`

DigitSeparator :: `_`

IntLiteral ::

- BinaryIntLiteral
- OctalIntLiteral
- HexIntLiteral
- DecimalIntLiteral

DecimalIntLiteral :: NegativeSign? Digit (Digit | DigitSeparator)\*

BinaryIntLiteral :: NegativeSign? `0` BinaryIndicator BinaryDigit (BinaryDigit | DigitSeparator)\*

BinaryIndicator :: one of `b` `B`

BinaryDigit :: one of `0` `1`

OctalIntLiteral :: NegativeSign? `0` OctalIndicator OctalDigit (OctalDigit | DigitSeparator)\*

OctalIndicator :: one of `o` `O`

OctalDigit :: one of `0` `1` `2` `3` `4` `5` `6` `7`

HexIntLiteral :: NegativeSign? `0` HexIndicator HexDigit (HexDigit | DigitSeparator)\*

HexIndicator :: one of `x` `X`

HexDigit :: one of

- `0` `1` `2` `3` `4` `5` `6` `7` `8` `9`
- `a` `b` `c` `d` `e` `f`
- `A` `B` `C` `D` `E` `F`

FloatLiteral ::

- LeadingFloatLiteral
- TrailingFloatLiteral
- ExponentFloatLiteral

LeadingFloatLiteral :: NegativeSign? Digit+ `.` Digit\* ExponentPart?

TrailingFloatLiteral :: NegativeSign? Digit\* `.` Digit+ ExponentPart?

ExponentFloatLiteral :: NegativeSign? Digit+ ExponentPart

ExponentPart :: ExponentIndicator NegativeSign? Digit+

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
- `\` EscapeSequence

EscapeSequence ::

- SimpleEscape
- HexEscape
- UnicodeEscape

SimpleEscape :: one of `n` `r` `t` `v` `e` `f` `$` `"` `\`

HexEscape :: `x` HexDigit HexDigit?

UnicodeEscape :: `u` `{` HexDigit+ `}`

## Types

Type : Expression

Expression : ConditionalType

ConditionalType :

- LogicalType ConditionalSuffix?
- Variable ConditionalSuffix

ConditionalSuffix : ConditionalOperator ConditionalOperand `?` Type `:` Type

ConditionalOperand :

- Type
- Variable

ConditionalOperator : one of `is` `is not` `>=` `<=` `<` `>`

## Logical Types

LogicalType : UnionType

UnionType : IntersectionType (`|` UnionType)?

IntersectionType : UnaryType (`&` IntersectionType)?

UnaryType : NullableType

NullableType : `?`? PostfixType

## List and Offset Access Types

PostfixType : PrimaryType TypeSuffix\*

TypeSuffix :

- ListSuffix
- OffsetSuffix

ListSuffix : `[` `]`

OffsetSuffix : `[` Type `]`

## Primary Types

PrimaryType :

- `(` Type `)`
- ThisVariable
- LiteralType
- CallableType
- NamedType

## Names

Name :

- FullyQualifiedName
- RelativeName

FullyQualifiedName : `\` Identifier (`\` Identifier)\*

RelativeName : Identifier (`\` Identifier)\*

Identifier :

- Name
- ReservedWord

## Named and Generic Types

NamedType : Name (TemplateArguments | ShapeFields)?

TemplateArguments : `<` TemplateArgument (`,` TemplateArgument)\* `,`? `>`

TemplateArgument : AttributeGroups? (TemplateArgumentHint | TemplateArgumentType)

TemplateArgumentHint : Identifier Type

TemplateArgumentType : Type

## Literal and Constant Types

LiteralType :

- BoolLiteral
- NullLiteral
- IntLiteral
- FloatLiteral
- StringLiteral
- ConstantType

ConstantType :

- Name
- ClassConstant
- ConstantMask

ClassConstant : Name `::` Identifier

ConstantMask :

- Name `*`
- Name `::` Identifier `*`
- Name `::` `*`

## Shape Types

ShapeFields : `{` ShapeBody? `,`? `}`

ShapeBody :

- ShapeFieldList (`,` UnsealedShape)?
- UnsealedShape

ShapeFieldList : ShapeField (`,` ShapeField)\*

ShapeField : AttributeGroups? (ExplicitField | ImplicitField)

ExplicitField : ShapeKey `?`? `:` ShapeValue

ImplicitField : ShapeValue

ShapeKey :

- Identifier
- IntLiteral
- StringLiteral
- ConstantMask
- ClassConstant

ShapeValue : Type

UnsealedShape : `...` TemplateArguments?

## Callable Types

CallableType : Name `(` CallableParameters? `)` CallableReturnType?

CallableParameters : CallableParameter (`,` CallableParameter)\* `,`?

CallableReturnType : `:` Type

CallableParameter : AttributeGroups? CallableParameterBody `=`?

CallableParameterBody :

- `...`? Type ParameterModifiers? Variable?
- ParameterModifiers? Variable

ParameterModifiers :

- `&` `...`?
- `...` `&`?

## Attributes

AttributeGroups : AttributeGroup+

AttributeGroup : `#[` AttributeList `,`? `]`

AttributeList : Attribute (`,` Attribute)\*

Attribute : Name AttributeArguments?

AttributeArguments : `(` AttributeArgument (`,` AttributeArgument)\* `,`? `)`

AttributeArgument : Type
