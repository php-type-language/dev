# @template-implements

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@template-implements` is a recognized alias for
[@implements](implements-tag.md): the two spellings parse identically
and produce the exact same `ImplementsTag` instance. The longer,
`template-` prefixed spelling makes the connection to the interface's
own `@template` parameters explicit, which some codebases prefer.

```
"@implements" <Type> [ <Description> ]
"@template-implements" <Type> [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> /**
>  * @implements ArrayAccess<int, User>
>  */
> final class UserList
>     implements ArrayAccess
> {
> }
> ```

</tab>
<tab title="Alias">

> ```php
> /**
>  * @template-implements ArrayAccess<int, User>
>  */
> final class UserList
>     implements ArrayAccess
> {
> }
> ```

</tab>
</tabs>

Both snippets above parse to the same `ImplementsTag`, exposing the
same `$type` property. See [@implements](implements-tag.md) for the
canonical reference on this tag's grammar, exposed shape and origin.
