# BoolType

<link-summary>
Checks for a boolean value and returns a boolean value.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\BoolType</code>
    </p>
</tldr>

Checks for a boolean value and returns a boolean value.

<warning>
Below are examples without using <a href="type-coercers.md">type coercers</a> 
(equivalent to the <code>strictTypes: true</code> configuration option).
</warning>

<code-block lang="php">
$mapper->isNormalizable(true, 'bool'); // bool(true)
$mapper->isNormalizable(42, 'bool');   // bool(false)
</code-block>

<code-block lang="php">
$mapper->normalize(true, 'bool'); // bool(true)
$mapper->normalize(42, 'bool');   // bool(false)
</code-block>

