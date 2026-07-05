# PHP TypeLang Specification

_Current Working Draft_

**Introduction**

This is the specification for **TypeLang**, a language for describing the types
of values in the PHP ecosystem. TypeLang is not a programming language and has no
runtime: a TypeLang document is a single _type expression_ that denotes a set of
values, such as those commonly written inside PHP docblocks (phpdoc), attributes,
or configuration.

The syntax of TypeLang is based on, and is intentionally compatible with, the
type grammars popularized by the static analysis tools
[PHPStan](https://phpstan.org/writing-php-code/phpdoc-types) and
[Psalm](https://psalm.dev/docs/annotating_code/type_syntax/atomic_types/), while
unifying and extending them into a single, formally described grammar.

This specification describes the **syntax** of the language — the lexical
structure of the source text and the syntactic structure of the resulting
abstract syntax tree (AST). It deliberately does **not** prescribe the
**semantics** of types (whether a referenced type physically exists, whether a
generic accepts the given number of arguments, or whether two types are
compatible). Those concerns are left to the implementation that consumes a
TypeLang document.

A conforming implementation of TypeLang must accept every document described as
valid by the grammar in this specification, and must reject every document that
the grammar describes as invalid.

This specification is maintained by the
[PHP Type Language](https://github.com/php-type-language) project.

# [Overview](Section%201%20--%20Overview.md)

# [Lexical Grammar](Section%202%20--%20Lexical%20Grammar.md)

# [Types](Section%203%20--%20Types.md)

# [Structural Types](Section%204%20--%20Structural%20Types.md)

# [Appendix: Notation Conventions](Appendix%20A%20--%20Notation%20Conventions.md)

# [Appendix: Grammar Summary](Appendix%20B%20--%20Grammar%20Summary.md)
