# A. Appendix: Notation Conventions

This specification uses a number of notation conventions to describe the
language grammar. This appendix explains those notations so as to avoid
ambiguity, and is itself non-normative: it describes how to read the
productions found elsewhere in this document, but introduces no new
requirement of its own.

## Context-Free Grammar

A context-free grammar consists of a number of _productions_. Each production
has an abstract symbol, called a _non-terminal_, as its left-hand side, and
one or more sequences of non-terminal symbols and terminal characters as its
right-hand side.

Starting from a single goal non-terminal ({Type}, for TypeLang), the grammar
describes a language: the set of character sequences obtained by repeatedly
replacing a non-terminal with one of its right-hand sides until only
terminals remain.

Terminals are written in a monospace font, either as a specific character or
sequence (for example {`|`} or {`is`}), or as prose describing a code point
(for example {"New Line (U+000A)"}).

A production with a single definition is written on one line:

NonTerminalWithSingleDefinition : NonTerminal `terminal`

A production with several alternative definitions is written as a list:

NonTerminalWithManyDefinitions :

- OtherNonTerminal `terminal`
- `terminal`

A definition may refer to itself to describe a repetitive sequence:

ListOfLetterA :

- ListOfLetterA `a`
- `a`

## Lexical and Syntactic Grammar

TypeLang is defined by two grammars. The _lexical grammar_ matches patterns
of source characters into _tokens_; the _syntactic grammar_ matches patterns
of tokens into the abstract syntax tree.

A lexical grammar production is distinguished by a double colon `::`. No
{Ignored} characters may appear between the terminals of a lexical
production.

Word :: Letter+

A syntactic grammar production is distinguished by a single colon `:`.
{Ignored} tokens may appear before or after any terminal token of a syntactic
production.

Phrase : Word+

## Grammar Notation

**one of.** A production whose alternatives are each a single terminal may be
written compactly with the phrase "one of":

Operator : one of `|` `&` `?`

is shorthand for

Operator :

- `|`
- `&`
- `?`

**Optionality.** A subscript-style suffix `?` denotes an optional symbol: one
sequence including it, and one excluding it.

Nullable : `?`? Type

is shorthand for

Nullable :

- `?` Type
- Type

**Lists.** A suffix `*` denotes zero or more repetitions of a symbol; a
suffix `+` denotes one or more. For example, {Identifier+} matches a
non-empty run of {Identifier}.

**Constraints (but not).** The phrase "but not" excludes certain expansions
that would otherwise be permitted.

NonReserved : Name but not `true` or `false` or `null`

means that a {NonReserved} may be any {Name} except those three sequences.

**Lookahead Restrictions.** A restriction of the form `[lookahead != X]`
states that the production must not be followed by `X`. Lookahead
restrictions remove ambiguity and, together with longest-match scanning,
ensure a single valid lexical analysis. For example:

NameToken :: NameStart NameContinue\* [lookahead != NameContinue]

makes explicit that a {NameToken} is always the longest possible sequence,
and cannot be followed by another {NameContinue} character.

**Ordered Choice.** Unless a production states otherwise, its alternatives
are attempted in the order in which they are written, and the first
alternative that matches at the current position is selected; a later
alternative is attempted only once every earlier alternative has failed to
match. This discipline applies uniformly to every alternation in both the
lexical and the syntactic grammar and, in particular, is what allows this
specification to describe an unambiguous grammar despite alternatives whose
languages overlap — for example, the five alternatives of
[PrimaryType](#sec-Primary-Types), or the five alternatives of
[ShapeKey](#sec-Shape-Fields). This is analogous to the ordered choice of a
parsing expression grammar, and is a stronger guarantee than the mere absence
of ambiguity provided by a classical context-free grammar.

## Grammar Semantics

Some productions are accompanied by a **Static Semantics** description, which
explains how a conforming implementation is to interpret the matched source
beyond merely accepting it — for example, how the radix of an integer
literal is determined, or how an out-of-range value is clamped. Static
semantics never alter which documents are accepted; they only describe the
value or node that a valid document denotes.

## Examples and Counter-Examples

Code blocks in this document illustrate the grammar. A block presented
without qualification denotes a **valid** document. A block explicitly
introduced as a _counter-example_ denotes an **invalid** document, and is
typically followed by the kind of error a conforming implementation is
expected to raise. Such error messages are illustrative and non-normative;
their exact wording is not prescribed by this specification, and an
implementation MAY report a different message, provided that it rejects the
document (see [Conformance](#sec-Conformance)).
