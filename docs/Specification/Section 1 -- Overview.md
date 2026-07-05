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
symbol) in docblocks, however they all share one common feature: the use of a
type declaration syntax. In the example above, the type of the `@param` tag is
`int[]`, the type of `@return` is `non-empty-string`, and the type of `@throws`
is `\OutOfBoundsException`.

TypeLang describes exactly this syntax. A complete TypeLang document is a single
_type expression_; it can be embedded in docblocks, attributes, configuration
files, IDE tooling, serializers, validators, mappers, or anywhere else a precise
description of a PHP value is required.

## Design Principles

The language is defined according to the following principles.

**Syntax, not semantics.** This specification defines only what is _well-formed_.
The parser does not impose restrictions on type naming, the number of generic
arguments, the existence of referenced classes or constants, or the logical
consistency of a type. For example, the document `int<string, max>` is
syntactically valid even though no implementation is likely to accept it
semantically. Validation of such concerns is the responsibility of the consuming
implementation.

**Superset compatibility.** TypeLang aims to parse every type expression that is
accepted by PHPStan or Psalm, and to resolve ambiguities and inconsistencies
between them in favour of the most permissive well-defined interpretation.

**A single root.** Unlike a general-purpose language, a TypeLang document is not
a list of statements. It is exactly one {Type} (see [Types](#sec-Types)). There
are no declarations, no imports, and no execution.

## Document

Document : Type

A TypeLang {Document} is a single {Type} expression. The source text of a
document, after removal of {Ignored} tokens, must be described in full by a
single {Type}; any trailing tokens that cannot be consumed by {Type} are a
syntax error.

This is an example of a valid document combining several features of the
language:

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

Throughout this document, code examples are presented in fenced blocks. Examples
that denote valid documents are shown directly; examples that denote a syntax
error are explicitly marked as counter-examples and are accompanied by the kind
of error a conforming parser is expected to raise.
