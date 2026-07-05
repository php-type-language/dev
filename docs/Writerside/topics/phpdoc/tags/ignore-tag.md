# @ignore

<primary-label ref="phpdoc-component"/>

The `@ignore` tag tells documentation-generating tooling to leave the
decorated element out of its output entirely, as if it did not exist. This
is stronger than [@internal](internal-tag.md), which still documents an
element but flags it as an implementation detail not meant for consumers:
`@ignore` removes it from the generated documentation altogether, which is
useful for scaffolding, deprecated leftovers, or helpers that would only
confuse readers of the public reference.

```
"@ignore" [ <Description> ]
```

> Hiding a helper method from generated documentation.
> ```php
> /**
>  * @ignore
>  */
> protected function debugDump(): void {}
> ```

> With a description explaining why it's excluded.
> ```php
> /**
>  * @ignore Scaffolding left over from the migration; will be
>  *         removed.
>  */
> ```

Parsing an `@ignore` tag produces an `IgnoreTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class IgnoreTag extends FlagTag {}
```

`@ignore` is still part of phpDocumentor's current tag reference and is
not deprecated —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/ignore.html).
It is parsed here for compatibility with classic-style docblocks.
