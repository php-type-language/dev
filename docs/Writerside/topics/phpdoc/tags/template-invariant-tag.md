# @template-invariant

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@template-invariant` is a recognized alias for
[@template](template-tag.md) — not a distinct tag. A plain `@template` is
already invariant by default; this spelling exists only for declarations
that want to say so explicitly, mirroring how
[@template-covariant](template-covariant-tag.md) and
[@template-contravariant](template-contravariant-tag.md) name their own
variance.

```
"@template-invariant" <Name>
    [ "of" <Type> ] [ "=" <Type> ]
    [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> /**
>  * @template T
>  */
> class Collection {}
> ```

</tab>
<tab title="Alias">

> ```php
> /**
>  * @template-invariant T
>  */
> class Collection {}
> ```

</tab>
</tabs>

Both spellings above parse to the exact same `TemplateTag` instance. See
[@template](template-tag.md) for the full grammar, the
`$parameter`/`$bound`/`$default` shape it exposes, and how the `"of"` and
`"="` clauses work.
