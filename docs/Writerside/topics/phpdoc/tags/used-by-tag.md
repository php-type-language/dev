# @used-by

<primary-label ref="phpdoc-component"/>

The `@used-by` tag documents that the described element is depended upon
by the referenced one — the inverse of [@uses](uses-tag.md). Where
[@see](see-tag.md) simply says "read also", `@uses`/`@used-by` express an
actual dependency worth tracking: if a change to this element could break
the referenced one, `@used-by` is how that relationship is written down.
The two tags are typically used in pairs — if `A` is annotated `@uses B`,
`B` is often annotated back with `@used-by A` — so the dependency is
discoverable by reading either side.

```
"@used-by" <Reference> [ <Description> ]
```

> A low-level helper documenting a caller it must stay compatible with.
> ```php
> /**
>  * @used-by Mailer::send()
>  */
> function normalizeAddress(
>     string $address,
> ): string {
>     /* ... */
> }
> ```

> With a description explaining the dependency.
> ```php
> /**
>  * @used-by ReportExporter::export()
>  *     Relies on the column order
>  *     returned here.
>  */
> function columns(): array
> {
>     /* ... */
> }
> ```

Parsing a `@used-by` tag produces a `ReferenceTag` instance narrowed to a
`CodeReference` (a class, function, method, constant, property, or
variable — never an external URI), exposing `$reference`.

```php
final class UsedByTag extends ReferenceTag
{
    // extends ReferenceTag<CodeReference>
    // directly; adds nothing beyond the
    // inherited $reference, $name and
    // $description.
}
```

<note>
phpDocumentor does not give <code>@used-by</code> its own reference page —
it is documented only on the same page as <a href="uses-tag.md">
<code>@uses</code></a>. The PSR-19 draft, in turn, spells this tag
<code>@usedby</code> (no hyphen) in its §5.17; this library follows
phpDocumentor's hyphenated <code>@used-by</code> spelling instead.
</note>

Defined by
[phpDocumentor](https://docs.phpdoc.org/guide/references/phpdoc/tags/uses.html),
alongside its counterpart [@uses](uses-tag.md).
