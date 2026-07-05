# @template-contravariant

<primary-label ref="phpdoc-component"/>

`@template-contravariant` declares a generic type parameter exactly like
[@template](template-tag.md), but with the mirror-image promise of
[@template-covariant](template-covariant-tag.md): the parameter is only
ever used in "input" positions — accepted as an argument — never returned.
That lets a static analyzer substitute in the *other* direction: a
`Comparator<Animal>` is safe to use anywhere a `Comparator<Dog>` is
expected, because anything that can compare animals in general can compare
dogs in particular. Ordinary invariant `@template` parameters, and covariant
ones, do not allow that substitution.

```
"@template-contravariant" <Name>
    [ "of" <Type> ] [ "=" <Type> ]
    [ <Description> ]
```

> A comparator whose type parameter only ever appears as an argument.
> ```php
> /**
>  * @template-contravariant T
>  */
> interface Comparator
> {
>     /**
>      * @param T $a
>      * @param T $b
>      */
>     public function compare(mixed $a, mixed $b): int;
> }
> ```

Parsing a `@template-contravariant` tag produces the same kind of tag as
`@template`, exposing `$parameter`, `$bound`, and `$default`; see
[@template](template-tag.md) for what each of those means and how the
`"of"` and `"="` clauses work, and
[@template-covariant](template-covariant-tag.md) for the opposite variance.

```php
final class TemplateContravariantTag extends TypeParameterTag
{
    // Same $parameter/$bound/$default shape as TemplateTag.
}
```

PHPStan supports contravariant template parameters — see its
[generics variance error reference](https://phpstan.org/error-identifiers/generics.variance),
though that page does not spell out the `@template-contravariant` tag name
verbatim. Psalm's own
[templated annotations](https://psalm.dev/docs/annotating_code/templated_annotations/)
page does not separately call out contravariance the way it does for
covariant parameters.
