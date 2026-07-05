# @link

<primary-label ref="phpdoc-component"/>
<secondary-label ref="inline-tag"/>

The `@link` tag points from the described element to an external web
resource: a specification, an RFC, a blog post explaining a tricky
algorithm, or a page in an external vendor's documentation. Unlike
[@see](see-tag.md), which can reference either a symbol in the codebase or
a URI, `@link` only ever points *outside* the codebase — its argument must
be a well-formed URI, never a class, method, or constant reference.

```
"@link" <URI> [ <Description> ]
```

> A bare link to an external specification.
> ```php
> /**
>  * @link https://www.rfc-editor.org/rfc/rfc3986
>  */
> ```

> A link with a description explaining why it's
> relevant.
> ```php
> /**
>  * @link https://example.com/docs/rate-limits Applies to all public
>  *       endpoints.
>  */
> ```

Parsing a `@link` tag produces a `LinkTag` instance:

```php
final class LinkTag extends ReferenceTag
{
    public function __construct(
        string $name,
        UriReference $uri,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct(
            $name,
            $uri,
            $description,
        );
    }
}
```

It exposes `$reference` — the parsed `UriReference` —
inherited from `ReferenceTag`, not `$uri`: that name is
only the constructor parameter used before forwarding the
value to the parent class.

`@link` may also be used **inline**, nested inside a
description as a `{@link ...}` sequence, rather than only
on its own line:

```php
/**
 * Retries follow the backoff described in
 * {@link https://example.com/docs/retries}.
 */
```

Defined by the [PSR-19 draft](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#57-link)
and [phpDocumentor's own page](https://docs.phpdoc.org/guide/references/phpdoc/tags/link.html);
the underlying URI syntax follows
[RFC 3986](https://www.rfc-editor.org/rfc/rfc3986).
