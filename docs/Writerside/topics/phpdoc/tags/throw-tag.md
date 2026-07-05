# @throw

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@throw` is a recognized alias for [@throws](throws-tag.md) — not a
distinct tag. It exists because dropping the trailing "s" is a fairly
common typo in code, and this component's own source registers it as an
alias for that reason rather than as a separate convention.

```
"@throw" <Type> [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> /**
>  * @throws NotFoundException
>  */
> function fetchUser(int $id): User
> {
>     // ...
> }
> ```

</tab>
<tab title="Alias">

> ```php
> /**
>  * @throw NotFoundException
>  */
> function fetchUser(int $id): User
> {
>     // ...
> }
> ```

</tab>
</tabs>

Both spellings above parse to the exact same `ThrowsTag` instance. See
[@throws](throws-tag.md) for the full grammar and the `$type` property it
exposes.
