# @deprecated

<primary-label ref="phpdoc-component"/>

The `@deprecated` tag marks an element as no longer recommended for use. It
warns consumers that the element may be removed in a future release, and
optionally names the version starting from which it should be considered
deprecated and what to use in its place.

```
"@deprecated" [ <Version> ] [ <Description> ]
```

> Naming the version and pointing to a replacement.
> ```php
> /**
>  * @deprecated 2.0 Use Mailer::send() instead.
>  */
> public function dispatch(Message $message): bool
> ```

> Without a version, just explaining the reason.
> ```php
> /**
>  * @deprecated No longer needed now that transport is chosen
>  *             automatically.
>  */
> public function withTransport(Transport $transport): static
> ```

Parsing a `@deprecated` tag produces a `DeprecatedTag` instance exposing
`$version` — the version since which the element is deprecated, or `null`
when none was given — alongside the inherited `$description`.

```php
final class DeprecatedTag extends VersionedTag {}
```

`VersionedTag` itself declares the `$version` property (and the
constructor that fills it in) — `DeprecatedTag` adds nothing of its own.

<note>
IDEs and static analyzers commonly treat <code>@deprecated</code> as more
than documentation: every call site of the annotated element is typically
flagged with a warning, strike-through, or similar visual marker.
</note>

Defined by the [PSR-19 draft PHPDoc Tags proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#54-deprecated)
and [phpDocumentor's tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/deprecated.html).
