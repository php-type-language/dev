# @returns

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@returns` is not a distinct tag — it is a recognized alias for
[`@return`](return-tag.md). The library's own source comment marks
it as "a fairly common typo in code," so rather than rejecting it,
the parser accepts `@returns` and produces the exact same tag that
`@return` would.

```
"@returns" <Type> [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> /**
>  * @return array{id: int, name: string}
>  */
> ```

</tab>
<tab title="Alias">

> ```php
> /**
>  * @returns array{id: int, name: string}
>  */
> ```

</tab>
</tabs>

Both spellings above parse into an identical `ReturnTag` instance,
exposing the same `$type` and `$description`. See
[@return](return-tag.md) for the full description of the tag's shape
and behavior.
