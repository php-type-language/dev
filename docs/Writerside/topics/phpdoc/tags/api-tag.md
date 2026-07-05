# @api

<primary-label ref="phpdoc-component"/>

The `@api` tag marks an element as part of the stable, public API of its
package. It tells both human readers and static analysis tools that the
element is safe to depend on: unlike the rest of an internal or
work-in-progress codebase, its signature is not expected to change between
releases without a deprecation notice.

```
"@api" [ <Description> ]
```

> A class marked as part of the public API.
> ```php
> /**
>  * @api
>  */
> final class Mailer
> {
> }
> ```

> With a description explaining the guarantee.
> ```php
> /**
>  * @api Stable since 2.0; changes follow
>  *      semantic versioning.
>  */
> public function send(
>     Message $message,
> ): bool
> ```

Parsing an `@api` tag produces an `ApiTag` instance. Being a pure marker, it
adds nothing beyond the `$name` and optional `$description` every
[tag](phpdoc.md#tag) already carries.

```php
final class ApiTag extends FlagTag {}
```

<tip>
<code>@api</code> is often used as the counterpart to
<a href="internal-tag.md">@internal</a>: together the two tags divide a
codebase into what consumers may rely on and what they may not.
</tip>

Defined by the [PSR-19 draft PHPDoc Tags proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#51-api)
and [phpDocumentor's tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/api.html).
