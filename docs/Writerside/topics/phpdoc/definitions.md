# Definitions

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

A **definition** is what turns a raw `@tag` line into a [tag object](tags.md).
There is exactly one per tag, and it declares two things: the shape of the line
that follows `@tagname`, and how to build the tag from what that shape matched.

A definition is a `TagDefinitionInterface` implementation — in practice, almost
always written by extending the `TagDefinition` base class, which takes care of
the boilerplate and leaves only one method to fill in: `create()`.

Take the [`@param`](param-tag.md) tag, whose line is
`"@param" <Type> <Variable> [ <Description> ]`:

```php
final class ParamTagDefinition extends TagDefinition
{
    public const string NAME = 'param';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            spec: Spec::sequence(
                Spec::rule(TypeCombinator::NAME, 'type'),
                Spec::rule(VariableCombinator::NAME, 'variable'),
                Spec::maybe(
                    Spec::rule(DescriptionCombinator::NAME, 'description'),
                ),
            ),
            placement: TagPlacement::Block,
        );
    }

    public function create(string $name, TagPayload $result): ParamTag
    {
        return new ParamTag(
            name: $name,
            statement: $result->get('type'),
            variable: $result->get('variable'),
            description: $result->find('description'),
        );
    }
}
```

The constructor hands the base class three things:

* **`name`** — the canonical tag name, without the leading `@`.
* **`spec`** — the grammar of the line's body.
* **`placement`** — where the tag may appear (see [below](#inline-tags)).

<tip>
The <code>spec</code> is written with the <code>Spec</code> grammar DSL —
<code>Spec::sequence()</code>, <code>Spec::rule()</code>,
<code>Spec::maybe()</code> and friends. See <a href="grammar.md">Grammar</a>
for how it is assembled, and <a href="combinators.md">Combinators</a> for the
named building blocks (<code>TypeCombinator</code>, <code>VariableCombinator</code>,
...) it reaches into.
</tip>

## Building the Tag

`create()` receives a `TagPayload` carrying whatever the grammar captured, and
returns the finished [tag](tags.md). `$result->get('type')` fetches a required
capture (it throws if nothing was captured under that alias);
`$result->find('description')` fetches an optional one, returning `null` when it
did not match.

<tip>
The values in the payload are addressed by the aliases declared in the
grammar — the second argument to <code>Spec::rule()</code>. See
<a href="grammar.md#captures">Captures</a> for how they are named and read back,
including <code>getAll()</code> for a value that repeats.
</tip>

A tag body that does not match its `spec` at all never reaches `create()`: it
becomes an [`InvalidTag`](phpdoc.md#usage) before `create()` would even be
called, so a definition never has to guard against a malformed match itself.

## Inline Tags

Where a tag may appear is declared by the `placement:` argument, a
`TagPlacement` enum with three cases:

```php
enum TagPlacement
{
    // only inline, inside a description as a "{@tag}" sequence
    case Inline;
    // only as a block tag, occupying its own line
    case Block;
    // usable both inline and as a block tag
    case Any;
}
```

A definition that omits the argument defaults to `TagPlacement::Any`. A tag
whose placement allows it inline may be used, in addition to standing on its own
line, nested inside a description as a `{@tag ...}` sequence with balanced
braces — parsing it there produces the exact same kind of tag object either way.

Six built-in tags allow it: [`@see`](see-tag.md), [`@link`](link-tag.md),
[`@internal`](internal-tag.md) and [`@example`](example-tag.md) are `Any`, while
[`@inheritdoc`](inheritdoc-tag.md) and [`@source`](source-tag.md) are
`Inline`-only. Every other tag, `@param` included, is declared `Block` and is
never lifted out of running text: a `{@param}` written inside a sentence stays
exactly as written, as plain text, rather than being parsed.

## Where to Go Next

<deflist>
    <def title="Grammar">
        The <code>Spec</code> DSL a definition's <code>spec</code> is written
        in. See <a href="grammar.md">Grammar</a>.
    </def>
    <def title="Tags">
        The value objects a definition's <code>create()</code> returns. See
        <a href="tags.md">Tags</a>.
    </def>
    <def title="Platforms">
        How a definition is registered with the parser. See
        <a href="platforms.md">Platforms</a>.
    </def>
</deflist>
