# @template-use

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@template-use` is a recognized alias of [@use](use-tag.md): it supplies
the concrete type arguments for a generic trait pulled in with PHP's
`use` keyword. Both spellings parse to the exact same `UseTag` instance —
`@template-use` is not a distinct tag, just a different spelling for it,
and in practice it is the spelling seen more often in real-world
PHPStan and Psalm documentation than the bare `@use` form.

```
"@use" <Type> [ <Description> ]
"@template-use" <Type> [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> final class ProductRepository
> {
>    /** @use Cacheable<Product> */
>     use Cacheable;
> }
> ```

</tab>
<tab title="Alias">

> ```php
> final class ProductRepository
> {
>     /** @template-use Cacheable<Product> */
>     use Cacheable;
> }
> ```

</tab>
</tabs>

Both examples above produce an identical `UseTag`, exposing the same
`$type` — the `TypeNode` naming the trait with its type arguments —
alongside the inherited `$name` and `$description`.

See [@use](use-tag.md) for the full description of the tag, its grammar,
and its exposed shape.
