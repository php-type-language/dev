# @template-extends

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

`@template-extends` is a recognized alias for
[@extends](extends-tag.md): the two spellings parse identically and
produce the exact same `ExtendsTag` instance. The longer, `template-`
prefixed spelling makes the connection to the class's own
`@template` parameters explicit, which some codebases prefer.

```
"@extends" <Type> [ <Description> ]
"@template-extends" <Type> [ <Description> ]
```

<tabs>
<tab title="Canonical">

> ```php
> /**
>  * @template T
>  * @extends Repository<T>
>  */
> abstract class BaseRepository
>     extends Repository
> {
> }
> ```

</tab>
<tab title="Alias">

> ```php
> /**
>  * @template T
>  * @template-extends Repository<T>
>  */
> abstract class BaseRepository
>     extends Repository
> {
> }
> ```

</tab>
</tabs>

Both snippets above parse to the same `ExtendsTag`, exposing the same
`$type` property. See [@extends](extends-tag.md) for the canonical
reference on this tag's grammar, exposed shape and origin.
