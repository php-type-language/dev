# @suppress

<primary-label ref="phpdoc-component"/>
<secondary-label ref="psalm-tag"/>

The `@suppress` tag silences one or more named diagnostics that a static
analyzer would otherwise report for the described element, without disabling
analysis of the element altogether.

```
"@suppress" <IssueName> { "," <IssueName> }
    [ <Description> ]
```

<tabs>
<tab title="Examples">

> Silencing a single diagnostic.
> ```php
> /**
>  * @suppress UndefinedMethod
>  */
> ```

> Silencing several diagnostics at once, with a description explaining why.
> ```php
> /**
>  * @suppress UndefinedMethod, PossiblyUnusedMethod Called only from
>  *           tests.
>  */
> ```

</tab>
<tab title="Counterexamples">

> Issue names are separated by a comma; whitespace alone does not.
> ```php
> @suppress UndefinedMethod PossiblyUnusedMethod
> ```
> Only `UndefinedMethod` is read as the issue name; everything after it is
> parsed as the tag's description instead of a second issue.
> {style="warning"}

</tab>
</tabs>

Parsing a `@suppress` tag produces a `SuppressTag` instance exposing
`$issues`: the list of every comma-separated identifier, in the order they
were written (always at least one).

```php
final class SuppressTag extends Tag
{
    public function __construct(
        string $name,
        /**
         * @var non-empty-list<non-empty-string>
         */
        public readonly array $issues,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

<note>
Every major static analyzer ships an equivalent tag under its own vendor
prefix instead — <code>@psalm-suppress</code>, <code>@phpstan-ignore</code>,
<code>@phan-suppress-next-line</code>, and so on — each with slightly
different scoping rules (the current line, the next line, the whole element).
</note>

The plain, vendor-neutral spelling parsed here is
[Psalm's `@psalm-suppress`](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-suppress-someissuename)
in its bare form. PHPStan and Phan only recognize their own prefixed
spellings, not this bare one.
