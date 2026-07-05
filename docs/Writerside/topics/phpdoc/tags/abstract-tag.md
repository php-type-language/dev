# @abstract

<primary-label ref="phpdoc-component"/>

The `@abstract` tag declares the element it decorates as abstract — a
docblock-level stand-in for PHP's native `abstract` keyword, for use where
that keyword cannot actually be written: a magic member introduced through
[@method](method-tag.md) or [@property](property-tag.md), for instance, has
no native declaration to modify. It also sees occasional use on a real
abstract class or method simply to make the fact more visible in generated
documentation.

```
"@abstract" [ <Description> ]
```

> Flagging a magic method as one subclasses are expected to implement.
> ```php
> /**
>  * @method void handle(Event $event)
>  * @abstract
>  */
> abstract class Listener {}
> ```

> With a description explaining what implementers must provide.
> ```php
> /**
>  * @abstract Subclasses must return the storage backend to use.
>  */
> protected function makeStorage(): Storage
> ```

Parsing an `@abstract` tag produces an `AbstractTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already carries.

```php
final class AbstractTag extends FlagTag {}
```

`@abstract` belongs to the original phpDocumentor tag vocabulary, but
phpDocumentor 3 has since dropped it entirely: it is absent from the
current 27-tag reference index, and no `tags/abstract.html` page exists
any longer. Only the archived phpDocumentor 1.x manual still describes
it, as a legacy reference rather than current documentation —
[phpDocumentor 1.x manual, archived](https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.abstract.pkg.html).
This component still parses `@abstract` for compatibility with
docblocks written against that classic toolchain.
