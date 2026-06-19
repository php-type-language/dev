# MixedType

<link-summary>
Infers the type of the passed value and applies the appropriate conversion
rules according to the inferred type.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\MixedType</code>
    </p>
</tldr>

Infers the type of the passed value and applies the appropriate conversion
rules according to the inferred type.

<note>
The rules for type inference are defined in 
<a href="type-extractors.md">type extractors</a>.
</note>

<code-block lang="php">
$mixed->match(42, ...); // bool(true)
</code-block>

<warning>
The "mixed" type will return <code>true</code> in any case, even if the 
specified value is not supported by the platform
</warning>

<p></p>

<code-block lang="php">
// When normalizing, it will infer a type 
// dependent on the value
$mixed->cast(42, ...); // int(42)

// For example, similar to using "int" type
$int->cast(42, ...); // int(42)
</code-block>
