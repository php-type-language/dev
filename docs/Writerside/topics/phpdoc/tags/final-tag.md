# @final

<primary-label ref="phpdoc-component"/>

The `@final` tag declares that a class must not be extended, or that a
method must not be overridden any further. It is the documentation-level
counterpart to PHP's native `final` keyword, used in places the keyword
itself cannot reach — for example, to tell consumers that a method behaves
as final by convention in a class that isn't itself declared `final`, or in
generated stubs and interfaces where the native modifier does not apply.

```
"@final" [ <Description> ]
```

> Marking a method as final without declaring the whole class final.
> ```php
> /**
>  * @final Overriding this would break the transaction guarantees
>  *        below.
>  */
> public function commit(): void
> ```

> A plain marker on a class already close to its native counterpart.
> ```php
> /**
>  * @final
>  */
> class Money {}
> ```

Parsing a `@final` tag produces a `FinalTag` instance. Being a pure marker,
it adds nothing beyond the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already carries.

```php
final class FinalTag extends FlagTag {}
```

phpDocumentor documents `@final` on its own [tag reference page](https://docs.phpdoc.org/guide/references/phpdoc/tags/final.html);
unlike `@api`, `@author`, `@copyright`, `@deprecated`, and `@internal`,
it is not part of the PSR-19 draft's list of tags.
