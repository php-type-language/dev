# Constant Types

<show-structure for="chapter" depth="2"/>

Unlike [identifiers (type names)](basic-types.md), constants cannot contain
the dash (`-`) character. However, the grammar of the [type name](basic-types.md)
and the constant *are identical*, so this conflict of semantics will have to be
resolved independently during the implementation of a custom solution that uses
the TypeLang grammar.

[Namespaces](basic-types.md#namespace) specifying a reference to a constant are
also allowed.

> With this in mind, note that any constant is interpreted as a
> [named type](basic-types.md).
> {style="note"}

Given the complete identity of the grammar of constants with
[named type](basic-types.md), they cannot contain case-insensitive names
`true`, `false` and `null` of literals.

<tabs>
<tab title="Examples">

> Example of global constant (or type name)
> ```typescript
> JSON_THROW_ON_ERROR
> ```

> Example of namespaced constant (or type name)
> ```typescript
> pcov\version
> ```

</tab>
<tab title="Counterexamples">

> The standalone keywords (`true`) is NOT available as an <tooltip
> term="Identifier">Identifier</tooltip> regardless of case and is parsed as a
> <a href="literal-types.md" anchor="boolean-and-null">literal value</a> rather 
> than an <tooltip term="Identifier">Identifier</tooltip>.
> ```typescript
> TrUe
> ```
> {style="warning"}

</tab>
</tabs>

## Class Constants

Class constants begin with any [type name](basic-types.md), then contain 
a double colon (`::`) character, and then the constant name.

<tabs>
<tab title="Examples">

> Reference to a class constant in the global namespace.
> ```typescript
> ClassName::CONSTANT_NAME
> ```

> Reference a constant in a class that is located in some namespace.
> ```typescript
> Path\To\ClassName::ANOTHER_CONSTANT_NAME
> ```

</tab>
<tab title="Counterexamples">

> A constant in a class must be an <tooltip term="Identifier">
> Identifier</tooltip> and cannot contain its own namespace.
> ```typescript
> ClassName::SOME\ANY
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "\"
> ```
> {style="warning"}

</tab>
</tabs>

## Constant Masks

A reference to a certain set of constants can be defined using a mask. The
use of masks is identical to regular constants, but must be terminated with
an asterisk (`*`).

Prefixes on class constants can be omitted, so type will mean any class constant.


<tabs>
<tab title="Examples">

> Reference to any constant with the `JSON_*` prefix.
> ```typescript
> JSON_*
> ```

> Reference to any class constant with the `PREFIX_` prefix.
> ```typescript
> Path\To\ClassName::PREFIX_*
> ```

> Reference to any class constant
> ```typescript
> Path\To\ClassName::*
> ```

</tab>
<tab title="Counterexamples">

> It is not allowed to omit prefixes from global constants.
> ```typescript
> *
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "*"
> ```
> {style="warning"}

> The asterisk (`*`) must be the final character.
> ```typescript
> Path\To\ClassName::PREFIX_*_SUFFIX
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "_SUFFIX"
> ```
> {style="warning"}

</tab>
</tabs>