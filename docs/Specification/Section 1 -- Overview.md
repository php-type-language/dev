# Overview

TypeLang is a language for describing the _types_ of PHP values. The vast
majority of PHP developers encounter such type descriptions on a daily basis
inside docblocks:

```php
/**
 * @param int[] $param Example array parameter
 *
 * @return non-empty-string An example return type
 *
 * @throws \OutOfBoundsException in case of something went wrong
 */
function example(array $param): string {}
```

There are many possible variations of tags (identifiers starting with the `@`
symbol) in docblocks; however, they all share one common feature: the use of a
type declaration syntax. In the example above, the type of the `@param` tag is
`int[]`, the type of `@return` is `non-empty-string`, and the type of `@throws`
is `\OutOfBoundsException`.

TypeLang describes exactly this syntax. A complete TypeLang document is a
single _type expression_; it can be embedded in docblocks, attributes,
configuration files, IDE tooling, serializers, validators, mappers, or
anywhere else a precise description of a PHP value is required.

## Design Principles

The language is defined according to the following principles.

**Syntax, not semantics.** This specification defines only what is
_well-formed_. A conforming implementation MUST NOT impose restrictions on
type naming, on the number of generic arguments, on the existence of a
referenced class or constant, or on the logical consistency of a type. For
example, the document `int<string, max>` is syntactically valid even though no
implementation is likely to accept it semantically. Validation of such
concerns MAY be performed by the consuming implementation but is out of scope
for this specification.

**Superset compatibility.** TypeLang aims to parse every type expression
accepted by PHPStan or Psalm, and to resolve ambiguities and inconsistencies
between them in favour of the most permissive well-defined interpretation.

**A single root.** Unlike a general-purpose language, a TypeLang document is
not a list of statements. It consists of exactly one {Type} (see
[Types](#sec-Types)); a document contains no declarations, no imports, and no
execution.

## Conformance

A conforming implementation of TypeLang MUST accept every document that this
specification defines as valid, and MUST reject every document that this
specification defines as invalid. A conforming implementation MAY additionally
accept extensions to the grammar defined herein, provided that every document
accepted by such an extension would otherwise be rejected by this
specification; an implementation MUST NOT extend the grammar in a way that
causes a document valid under this specification to be assigned a different
syntactic structure.

This specification defines only syntax. Whether a given document is
_semantically_ meaningful (for example, whether a referenced class exists, or
whether the number of supplied generic arguments matches the number expected
by a referenced type) is a matter for the consuming implementation and is
outside the scope of conformance to this specification.

The illustrative error messages that accompany counter-examples throughout
this specification (see [Examples and Counter-Examples](#sec-Examples-and-Counter-Examples))
are non-normative. A conforming implementation is not required to reproduce
their exact wording, but MUST reject the corresponding document.

## Document

Document : Type

A TypeLang {Document} consists of a single {Type} expression. The source text
of a document, after removal of {Ignored} tokens, MUST be described in full by
a single {Type}; any trailing tokens that cannot be consumed by {Type}
constitute a syntax error.

The following is an example of a valid document combining several features of
the language:

```typescript
array{
    id: int<1, max>,
    name: non-empty-string,
    roles: list<App\Domain\Role>,
    parent?: ?self,
    ...
}
```

## Relationship to Other Tools

TypeLang is a strict syntactic superset of the common subset shared by PHPStan
and Psalm. Notable extensions beyond what those tools accept include, among
others:

- Binary (`0b1010`), octal (`0o42`, legacy `042`) and hexadecimal (`0xDEAD`)
  integer literals.
- Escape, hexadecimal and Unicode sequences inside double-quoted string
  literals.
- Global constant masks such as `JSON_*`.
- Attributes (`#[...]`) on template arguments, shape fields and callable
  parameters.
- A uniform conditional (ternary) type syntax that also permits comparison
  operators and Yoda-style operands.

A detailed feature-by-feature comparison is maintained alongside the project
documentation and is out of scope for this specification.

## A Note on Examples

Throughout this document, code examples are presented in fenced blocks.
Examples that denote valid documents are shown directly; examples that denote
a syntax error are explicitly marked as counter-examples and are accompanied
by the kind of error a conforming parser is expected to raise.

## Security and Implementation Considerations

TypeLang documents are frequently extracted from sources that an implementation
does not fully trust, such as docblocks or attributes authored by third-party
packages. Implementers SHOULD take the following into account.

**Recursion depth.** Several productions of this grammar are directly or
indirectly recursive (for example, a parenthesized {Type}, a generic
{TemplateArgument}, a shape field value, and a callable parameter or return
type may each themselves contain an arbitrary {Type}). An implementation that
evaluates such productions using unbounded native recursion MAY be vulnerable
to stack exhaustion when parsing a maliciously or accidentally deeply nested
document. An implementation SHOULD impose an implementation-defined limit on
nesting depth and MUST, upon reaching that limit, reject the document rather
than exhibit undefined behaviour.

**Input length.** This specification does not limit the length of a {Name},
the number of {TemplateArgument}s, the number of {ShapeField}s, or the overall
length of a document. An implementation intended for use with untrusted input
SHOULD impose implementation-defined limits on these quantities.

**Numeric ranges.** As described in [Integer](#sec-Integer), an integer
literal's value is clamped to the representable range of the implementation's
platform integer while the raw lexeme is preserved. Implementations that
convert a {FloatLiteral} to a native floating-point representation are subject
to the usual limitations of that representation (precision loss, rounding,
infinities) and SHOULD document their behaviour in such cases.
