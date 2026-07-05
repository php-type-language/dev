# @method

<primary-label ref="phpdoc-component"/>

The `@method` tag declares a "magic" method: one that a class makes callable
through `__call()` or `__callStatic()` rather than through an ordinary method
declaration, and that therefore does not otherwise appear anywhere in the
class's own source. It is written on the class itself, once per magic method,
using the same syntax as a [callable type](callable-types.md).

```
"@method" [ "static" ] [ <ReturnType> ]
    <CallableType> [ <Description> ]
```

> A magic instance method with no return type.
> ```php
> /**
>  * @method void setName(string $name)
>  */
> class Model {}
> ```

> A magic static method, with its return type inside the
> signature.
> ```php
> /**
>  * @method static static create(array $attributes)
>  */
> class Model {}
> ```

> The return type may instead be written before the
> signature; the two forms are equivalent.
> ```php
> /**
>  * @method int getId()
>  * @method getId(): int
>  */
> ```

Parsing a `@method` tag produces a `MethodTag` instance:

```php
final class MethodTag extends Tag
{
    public function __construct(
        string $name,
        public readonly CallableTypeNode $method,
        private readonly string $signature,
        public readonly bool $isStatic = false,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

It exposes:

* `$method` — the declared signature (its name, parameters
  and return type) as a `CallableTypeNode`.
* `$isStatic` — whether the leading `"static"` keyword was
  present.

The constructor also keeps the signature exactly as
written in a `$signature` property, but that one is
`private` — it's not part of the tag's public shape, kept
internally only so the tag can render itself back to
source faithfully.

A return type written before the signature is folded into
`$method`'s own return type rather than kept separately,
so both forms shown above end up producing the same
`$method` value.

Defined by the [PSR-19 draft](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#58-method)
and [phpDocumentor's own page](https://docs.phpdoc.org/guide/references/phpdoc/tags/method.html).
