# @param

<primary-label ref="phpdoc-component"/>

The `@param` tag documents a single argument of a function or method: the
type of value it accepts, and — since PHP's own type hints cannot express
everything a docblock can (a shape, a generic, a literal, a conditional
type) — often more precisely than the function's native signature does.

```
"@param" <Type> <Variable> [ <Description> ]
```

The type is parsed using the full [TypeLang grammar](introduction.md),
so anything documented elsewhere in this reference — a
[shape](shape-types.md), a [generic](generic-types.md), a
[conditional type](conditional-types.md) — is equally valid here.

> A plain scalar type.
> ```php
> /**
>  * @param int $amount
>  */
> ```

> A generic collection type, with a description.
> ```php
> /**
>  * @param list<User> $recipients The people to notify.
>  */
> ```

> A shape describing the accepted array structure.
> ```php
> /**
>  * @param array{name: string, age: int<0, max>} $profile
>  */
> ```

Parsing a `@param` tag produces a `ParamTag` instance exposing:

* `$type` — the documented `TypeNode`.
* `$variable` — the parameter's name, without the leading `$`.

```php
final class ParamTag extends TypedVariableTag
{
    // Inherits a computed `TypeNode $type` getter and
    // `string $variable` from TypedVariableTag, plus
    // `$name` and `$description` from the base `Tag`
    // class; adds nothing of its own.
}
```

One `@param` tag is expected per documented argument, each naming the
argument it describes; the order of the tags in the comment does not have to
match the order of the arguments in the signature.

<note>
PHPStan and Psalm additionally recognize a vendor-prefixed
<code>@phpstan-param</code> / <code>@psalm-param</code> spelling, typically
used to narrow a type beyond what a plain <code>@param</code> above it
already states. This component parses only the plain <code>@param</code>
tag.
</note>

Defined by [PSR-19 §5.10](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#510-param)
— FIG's proposed PHPDoc tags standard — and phpDocumentor's own
[`@param` reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/param.html).
