# @uses

<primary-label ref="phpdoc-component"/>

The `@uses` tag documents that the described element uses — depends on —
the referenced one. Where [@see](see-tag.md) is a loose "read also"
pointer, `@uses` (together with its inverse, [@used-by](used-by-tag.md))
is meant to express a real dependency relationship worth tracking: if the
referenced element changes behavior, the element carrying `@uses` may
break. This makes the relationship visible from the dependent side, even
when the referenced element carries no annotation of its own.

```
"@uses" <Reference> [ <Description> ]
```

> A method documenting the helper it relies on.
> ```php
> /**
>  * @uses normalizeAddress()
>  */
> function send(string $address): void
> {
>     /* ... */
> }
> ```

> With a description explaining the dependency.
> ```php
> /**
>  * @uses Config::DEFAULT_TIMEOUT Falls
>  *     back to this when none is given.
>  */
> function connect(
>     ?int $timeout = null,
> ): void {
>     /* ... */
> }
> ```

Parsing a `@uses` tag produces a `ReferenceTag` instance narrowed to a
`CodeReference` (a class, function, method, constant, property, or
variable — never an external URI), exposing `$reference`.

```php
final class UsesTag extends ReferenceTag
{
    // extends ReferenceTag<CodeReference>
    // directly; adds nothing beyond the
    // inherited $reference, $name and
    // $description.
}
```

Defined by the PSR-19 draft
([FIG proposed PHPDoc Tags, §5.17](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#517-uses))
and by
[phpDocumentor](https://docs.phpdoc.org/guide/references/phpdoc/tags/uses.html),
which also documents its inverse, [@used-by](used-by-tag.md), on the same
page.
