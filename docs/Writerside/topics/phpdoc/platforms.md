# Platforms

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

A **platform** is a named bundle of [tag definitions](definitions.md), aliases
and [combinators](combinators.md) — everything needed to teach the parser a
family of tags.

A platform implements `PlatformInterface`, four read-only properties:

```php
final class MoneyPlatform implements PlatformInterface
{
    public string $name = 'money';

    public iterable $aliases = [];

    public iterable $combinators = [];

    public iterable $tags {
        get => [
            MoneyTagDefinition::NAME => new MoneyTagDefinition(),
        ];
    }
}
```

* **`$name`** — a human-readable name for the platform.
* **`$tags`** — [definitions](definitions.md), keyed by each tag's canonical
  (lower-case) name.
* **`$aliases`** — alternative-spelling to canonical-name pairs, both
  lower-case (this is how `@template-implements` is treated as `@implements`, for instance).
* **`$combinators`** — any [combinator](combinators.md) the definitions
  reference that is not built in already, keyed by name.

<note>
<code>MoneyTagDefinition</code> only leans on the built-in <code>Integer</code>
and <code>Name</code> combinators, so its platform contributes none of its own.
A platform adds combinators only when its tags introduce grammar the standard
platform does not already provide — see
<a href="combinators.md#writing-one-of-your-own">Writing One of Your Own</a>.
</note>

## Registering It

The platform is handed to `DocBlockParser`'s constructor:

```php
$parser = new DocBlockParser(platforms: [
    new MoneyPlatform(),
]);

$block = $parser->parse('/** @money 100 USD */');
```

The `StandardPlatform` is always loaded first, so every built-in tag stays
available; the platforms passed in extend it, each overriding an entry only when
it reuses the same tag, alias or combinator name.

<note>
Constructed without arguments, <code>DocBlockParser</code> loads the
<code>PhpDocumentorPlatform</code> on top of the standard one, so the legacy
phpDocumentor tags are recognized out of the box. Passing your own
<code>platforms</code> replaces that default — re-add
<code>new PhpDocumentorPlatform()</code> to the list if you still want those tags.
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
