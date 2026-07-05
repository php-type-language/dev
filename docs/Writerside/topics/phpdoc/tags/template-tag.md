# @template

<primary-label ref="phpdoc-component"/>

The `@template` tag declares a generic type parameter on a class, interface,
trait, function, or method — a name such as `T` that can then be referenced
by other tags on the same element, most notably
[@param](param-tag.md) and [@return](return-tag.md), or by a subclass
through [@extends](extends-tag.md) and [@implements](implements-tag.md).
Without a `@template` declaration first introducing `T`, those tags would
have no way to know that `T` names a type variable rather than an unresolved
class reference.

```
"@template" <Name> [ "of" <Type> ] [ "=" <Type> ] [ <Description> ]
```

The optional `"of"` clause constrains what the parameter is allowed to be —
an upper bound — and the optional `"="` clause supplies a default type to
use when a use site omits the parameter entirely.

> A bare, unconstrained type parameter.
> ```php
> /**
>  * @template T
>  */
> class Collection {}
> ```

> A parameter bounded to throwable types only.
> ```php
> /**
>  * @template T of \Throwable
>  */
> class ExceptionHandler {}
> ```

> A parameter with both a bound and a default, used when it is left
> unspecified.
> ```php
> /**
>  * @template T of \Stringable = \Stringable
>  */
> class Renderer {}
> ```

Parsing a `@template` tag produces a `TemplateTag` instance exposing:

* `$parameter` — the declared name, such as `T`.
* `$bound` — the `TypeReference` after `"of"`, or `null` if there is none.
* `$default` — the `TypeReference` after `"="`, or `null` if there is none.

```php
abstract class TypeParameterTag extends Tag
{
    public function __construct(
        string $name,
        public readonly string $parameter,
        public readonly ?TypeReference $bound = null,
        public readonly ?TypeReference $default = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}

final class TemplateTag extends TypeParameterTag {}
```

A plain `@template` is invariant: analyzers treat `Collection<T>` as
unrelated to `Collection<U>` even when `T` and `U` are themselves related.
[@template-covariant](template-covariant-tag.md) and
[@template-contravariant](template-contravariant-tag.md) relax that in
opposite directions for parameters used only in output or only in input
positions. This component also accepts
[@template-invariant](template-invariant-tag.md) as an explicit alias for
the plain, unrestricted `@template`.

<note>
PHP itself has no native generics. This tag, together with its variance
variants and <code>@extends</code>/<code>@implements</code>/<code>@param</code>/<code>@return</code>,
is the convention that static analyzers such as PHPStan and Psalm rely on
to check generic code in plain PHP.
</note>

`@template` is not a phpDocumentor or PSR tag. It comes from the generics
conventions that PHPStan and Psalm each adopted independently:
[PHPStan's generics guide](https://phpstan.org/blog/generics-in-php-using-phpdocs)
and
[Psalm's templated annotations](https://psalm.dev/docs/annotating_code/templated_annotations/).
