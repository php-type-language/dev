# Platforms

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

A **platform** is a named bundle of [tag definitions](definitions.md), aliases
and [combinators](combinators.md) — everything needed to teach the parser a
family of tags.

A platform implements `PlatformInterface`, three methods, each returning an
`iterable`. The abstract `Platform` base class implements all three as empty,
so a platform extends it and overrides only what it contributes:

```php
final class MoneyPlatform extends Platform
{
    public function getTags(): iterable
    {
        return [
            MoneyTagDefinition::NAME => new MoneyTagDefinition(),
        ];
    }
}
```

* **`getTags()`** — [definitions](definitions.md), keyed by each tag's
  canonical name.
* **`getAliases()`** — alternative-spelling to canonical-name pairs, both
  lower-case (this is how `@template-implements` is treated as `@implements`, for instance).
* **`getCombinators()`** — any [combinator](combinators.md) the definitions
  reference that is not built in already, keyed by name. A value is a
  `CombinatorInterface` implementation or any `callable(Cursor): mixed`.

<note>
<code>MoneyTagDefinition</code> only leans on the built-in <code>Integer</code>
and <code>Name</code> combinators, so its platform contributes none of its own.
A platform adds combinators only when its tags introduce grammar the standard
platform does not already provide — see
<a href="combinators.md#writing-one-of-your-own">Writing One of Your Own</a>.
</note>

Tag names and aliases are matched case-insensitively: a definition registered
as `inheritDoc` also answers to `@inheritdoc`.

## Built-in Platforms

<table style="both">
    <tr>
        <td width="220">Platform</td>
        <td>Contributes</td>
    </tr>
    <tr>
        <td><code>StandardPlatform</code></td>
        <td>The <a href="standard-tags.md">Standard</a> and
            <a href="advanced-tags.md">Advanced</a> tags, their aliases and
            the core combinators. Always loaded.</td>
    </tr>
    <tr>
        <td><code>PhpDocumentorPlatform</code></td>
        <td>The <a href="phpdocumentor-tags.md">phpDocumentor</a> tags.</td>
    </tr>
    <tr>
        <td><code>PhpStanPlatform</code></td>
        <td>The <a href="phpstan-tags.md">PHPStan</a> tags and the
            <code>@phpstan-</code> spellings of the shared ones.</td>
    </tr>
    <tr>
        <td><code>PsalmPlatform</code></td>
        <td>The <a href="psalm-tags.md">Psalm</a> tags and the
            <code>@psalm-</code> spellings of the shared ones.</td>
    </tr>
    <tr>
        <td><code>PhanPlatform</code></td>
        <td>The <a href="phan-tags.md">Phan</a> tags and the
            <code>@phan-</code> spellings of the shared ones.</td>
    </tr>
    <tr>
        <td><code>PhpStormPlatform</code></td>
        <td>The <a href="phpstorm-tags.md">PhpStorm</a> tags.</td>
    </tr>
    <tr>
        <td><code>PhpCodeSnifferPlatform</code></td>
        <td>The <a href="phpcs-tags.md">PHP CodeSniffer</a> tags.</td>
    </tr>
</table>

## Registering It

The platform is handed to `DocBlockParser`'s constructor:

```php
$parser = new DocBlockParser(platforms: [
    new MoneyPlatform(),
]);

$block = $parser->parse('/** @money 100 USD */');
```

The `StandardPlatform` is always loaded first, so every standard tag stays
available; the platforms passed in extend it, each overriding an entry only when
it reuses the same tag, alias or combinator name.

<note>
<code>new DocBlockParser()</code> with no arguments loads <b>only</b> the
<code>StandardPlatform</code> — the standard tags, and nothing else. To load
every built-in platform as well (phpDocumentor, Psalm, PHPStan, Phan, PhpStorm
and PHP CodeSniffer), use the <code>DocBlockParser::createDefault()</code>
factory:

<code-block lang="php">
$parser = DocBlockParser::createDefault();
</code-block>

To register your own platform on top of the built-in tool families, pass it to
<code>createDefault()</code> via the <code>$additionalPlatforms</code> argument —
it is appended after the built-in platforms, so it extends them and overrides an
entry only when it reuses the same name:

<code-block lang="php">
$parser = DocBlockParser::createDefault(additionalPlatforms: [
    new MoneyPlatform(),
]);
</code-block>
</note>

The definitions that end up registered are exposed as `$parser->tags` (a
countable, iterable `TagRegistryInterface`), and the factory that builds tags
from them as `$parser->factory`.

<tip>
A <code>DocBlockParser</code> builds its whole tag and type grammar once, in its
constructor, and is otherwise stateless. Construct it once — platforms and
all — and reuse it across the application rather than per docblock.
</tip>

## Where to Go Next

<deflist>
    <def title="Definitions">
        What a <code>$tags</code> entry is. See
        <a href="definitions.md">Definitions</a>.
    </def>
    <def title="Combinators">
        What a <code>$combinators</code> entry is. See
        <a href="combinators.md">Combinators</a>.
    </def>
</deflist>
