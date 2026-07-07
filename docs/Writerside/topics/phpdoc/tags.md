# Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

A **tag** is the parsed result object — what a single `@tag` line becomes once
it has been read. Every tag carries at minimum a `$name` (without the leading
`@`) and an optional `$description`; a tag recognized by name exposes further,
tag-specific parts on top of that. A [`@param`](param-tag.md) additionally
exposes the argument's `$type` and `$variable`, a [`@see`](see-tag.md) exposes
what it `$reference`s, and so on.

The base class every tag extends is `Tag`, whose constructor takes just the
name and an optional description:

```php
class Tag implements TagInterface
{
    public function __construct(
        public readonly string $name = '',
        public readonly ?DescriptionInterface $description = null,
    ) {}
}
```

A tag of your own is a small, immutable value object holding whatever its line
parsed into. A hypothetical `@money` tag, documenting a monetary amount and a
currency, adds an `$amount` and a `$currency` on top of the inherited name and
description:

```php
final class MoneyTag extends Tag
{
    public function __construct(
        string $name,
        public readonly int $amount,
        public readonly string $currency,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

That is the whole of the tag. Nothing about *how* its line is read lives
here — that belongs to its [definition](definitions.md), which is what actually
constructs a `MoneyTag` from the parsed pieces.

<note>
A tag whose name has no registered definition at all falls back to this plain
<code>Tag</code>, with its entire suffix folded unparsed into the description.
</note>

## Where to Go Next

<deflist>
    <def title="Definitions">
        How a tag's line is parsed and the tag object built from it. See
        <a href="definitions.md">Definitions</a>.
    </def>
    <def title="Platforms">
        How a finished tag is registered with the parser. See
        <a href="platforms.md">Platforms</a>.
    </def>
</deflist>
