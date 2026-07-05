# @mixin

<primary-label ref="phpdoc-component"/>

Some classes expose members they never declare themselves, delegating to
another object at runtime through `__call`, `__get`, or similar magic
methods. PHP's own type system has no way to express that delegation, so an
IDE or static analyzer sees only the handful of methods actually written on
the class. `@mixin` tells it to also treat every public method and property
of the referenced type as available, restoring autocomplete and type
checking for code that would otherwise only work by accident at runtime.

```
"@mixin" <Type> [ <Description> ]
```

> A query builder that forwards undefined method calls to
> its underlying connection.
> ```php
> /**
>  * @mixin Connection
>  */
> class QueryBuilder
> {
>     public function __call(string $name, array $arguments): mixed {
>         return $this->connection
>             ->$name(...$arguments);
>     }
> }
> ```

Parsing a `@mixin` tag produces a `MixinTag` instance:

```php
final class MixinTag extends TypedTag
{
    // adds a computed $type getter on top of
    // TypedTag/Tag; nothing else.
}
```

It exposes `$type` — the `TypeNode` naming the
delegated-to type — along with the `$name` and
`$description` every tag carries.

`@mixin` is not a phpDocumentor or PSR tag: it originates
as a shared [PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics)
and [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#mixins)
convention, and this library follows that same, more
widely used, spelling.
