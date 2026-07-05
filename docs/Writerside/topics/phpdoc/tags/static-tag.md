# @static

<primary-label ref="phpdoc-component"/>

The `@static` tag declares the decorated element as static — a docblock
equivalent of PHP's native `static` keyword, used where that keyword cannot
actually be written. This matters most for magic members declared through
[@property](property-tag.md), which has no way to express staticness itself;
[@method](method-tag.md) already has its own inline `"static"` keyword for
magic methods, so a standalone `@static` tag is mostly relevant elsewhere.

```
"@static" [ <Description> ]
```

> Declaring a magic property as static.
> ```php
> /**
>  * @property Connection $default
>  * @static
>  */
> class ConnectionRegistry {}
> ```

> With a description explaining the intent.
> ```php
> /**
>  * @static Shared across all instances of the registry.
>  */
> ```

Parsing a `@static` tag produces a `StaticTag` instance. Being a pure marker,
it adds nothing beyond the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already carries.

```php
final class StaticTag extends FlagTag {}
```

`@static` is part of the original phpDocumentor tag vocabulary, but
phpDocumentor 3 has since dropped it entirely: it is absent from the
current tag reference index, and no `tags/static.html` page exists any
longer. Only the archived phpDocumentor 1.x manual still describes it,
as a legacy reference rather than current documentation —
[phpDocumentor 1.x manual, archived](https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.static.pkg.html).
It is supported here for classic-style docblocks.
