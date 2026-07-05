# @inherits

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@inherits` is a recognized alias for [@extends](extends-tag.md): the
two spellings parse identically and produce the exact same
`ExtendsTag` instance. Some tools favor this plainer English word in
place of the PHP keyword — this component accepts either.

```
"@extends" <Type> [ <Description> ]
"@inherits" <Type> [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> /**
>  * @extends Collection<User>
>  */
> final class UserCollection extends Collection {}
> ```

</tab>
<tab title="Alias">

> ```php
> /**
>  * @inherits Collection<User>
>  */
> final class UserCollection extends Collection {}
> ```

</tab>
</tabs>

Both snippets above parse to the same `ExtendsTag`, exposing the same
`$type` property. See [@extends](extends-tag.md) for the canonical
reference on this tag's grammar, exposed shape and origin.
