# @internal

<primary-label ref="phpdoc-component"/>
<secondary-label ref="inline-tag"/>

The `@internal` tag marks an element as belonging to the internal workings
of its own package rather than to its public API: consumers should not
depend on it, since it may change or disappear without a deprecation notice.
Used inline inside a description, it instead marks just the surrounding
portion of text as meant only for the package's own maintainers, letting
documentation generators strip that portion from public-facing output while
keeping the rest of the description intact.

```
"@internal" [ <Description> ]
```

> An entire method excluded from the public API.
> ```php
> /**
>  * @internal Used by the container to wire up lazy proxies.
>  */
> public function __setDependencies(array $dependencies): void
> ```

> An inline note tucked inside an otherwise public description.
> ```php
> /**
>  * Formats the message body. {@internal
>  * Encoding is UTF-8 for now; revisit once
>  * transports negotiate charsets.}
>  */
> public function format(Message $message): string
> ```

Parsing an `@internal` tag produces an `InternalTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class InternalTag extends FlagTag {}
```

<tip>
<code>@internal</code> is the counterpart to <a href="api-tag.md">@api</a>:
together the two tags divide a codebase into what consumers may rely on and
what they may not.
</tip>

Defined by the [PSR-19 draft PHPDoc Tags proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#56-internal)
and [phpDocumentor's tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/internal.html).
