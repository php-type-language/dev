# @template-covariant

<primary-label ref="phpdoc-component"/>

`@template-covariant` declares a generic type parameter exactly like
[@template](template-tag.md), but with an extra promise attached: the
parameter is only ever used in "output" positions — return types — never
accepted as an argument. That promise is what lets a static analyzer treat
`Collection<Dog>` as a subtype of `Collection<Animal>` whenever `Dog` is a
subtype of `Animal`, a substitution an ordinary invariant `@template`
parameter does not permit, since the analyzer cannot otherwise rule out the
parameter being consumed somewhere in a way that substitution would break.

```
"@template-covariant" <Name>
    [ "of" <Type> ] [ "=" <Type> ]
    [ <Description> ]
```

> A read-only collection whose item type only ever comes out, never
> goes in.
> ```php
> /**
>  * @template-covariant T
>  */
> final class Collection
> {
>     /** @param T $item */
>     private function __construct(private readonly mixed $item) {}
>
>     /** @return T */
>     public function get(): mixed
>     {
>         return $this->item;
>     }
> }
> ```

Parsing a `@template-covariant` tag produces the same kind of tag as
`@template`, exposing `$parameter`, `$bound`, and `$default`; see
[@template](template-tag.md) for what each of those means and how the
`"of"` and `"="` clauses work.

```php
final class TemplateCovariantTag extends TypeParameterTag
{
    // Same $parameter/$bound/$default shape as TemplateTag.
}
```

The opposite variance is
[@template-contravariant](template-contravariant-tag.md), for parameters
used only as input.

Both PHPStan and Psalm give `@template-covariant` dedicated coverage of
their own:
[PHPStan's write-up on template-covariant](https://phpstan.org/blog/whats-up-with-template-covariant)
and
[Psalm's templated annotations](https://psalm.dev/docs/annotating_code/templated_annotations/).
