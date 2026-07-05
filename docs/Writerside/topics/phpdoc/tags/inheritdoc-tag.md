# @inheritdoc

<primary-label ref="phpdoc-component"/>
<secondary-label ref="inline-tag"/>

The `@inheritdoc` tag tells documentation generators to pull in the
description and tags of the method or class being overridden or implemented,
instead of repeating them. It saves an overriding method from having to
restate a parent's contract word for word when it isn't changing that
contract, only its implementation.

```
"@inheritdoc" [ <Description> ]
```

> As a standalone tag, inheriting the parent docblock wholesale.
> ```php
> /**
>  * @inheritdoc
>  */
> public function send(Message $message): bool
> ```

> Used inline, to graft an addition onto the inherited
> text.
> ```php
> /**
>  * {@inheritdoc} Also flushes the queue before
>  * returning.
>  */
> public function send(Message $message): bool
> ```

Parsing an `@inheritdoc` tag produces an `InheritDocTag`
instance:

```php
final class InheritDocTag extends FlagTag {}
```

Being a pure marker, it adds nothing beyond the `$name`
and optional `$description` every [Tag](phpdoc.md#tag)
already carries — the description carries only the text
to append when the tag is used inline.

Defined by the [PSR-19 draft](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md)'s
`@inheritDoc` section and phpDocumentor's
[inheritance guide](https://docs.phpdoc.org/guide/guides/inheritance.html)
(its tags-reference page 404s for this one).
