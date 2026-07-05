# @return

<primary-label ref="phpdoc-component"/>

`@return` documents what a function or method gives back, frequently with
more precision than PHP's native return type hint allows — a shape, a
literal value, a generic, or a type that depends on one of the arguments.
It is one of the most commonly written tags, since even a fully typed
signature rarely captures the exact structure callers can rely on.

```
"@return" <Type> [ <Description> ]
```

> Narrowing a native `array` return type to its actual shape.
> ```php
> /**
>  * @return array{id: int, name: string}
>  */
> ```

> Documenting a generic collection returned by a factory method.
> ```php
> /**
>  * @return list<User> All currently active users.
>  */
> ```

Parsing a `@return` tag produces a `ReturnTag` instance exposing
`$type` — the parsed `TypeNode` — together with the `$name` and
`$description` shared by every tag.

```php
abstract class TypedTag extends Tag
{
    public TypeNode $type {
        get => $this->statement->type;
    }
}

final class ReturnTag extends TypedTag
{
    // inherits $name, $description, and the
    // computed $type property from TypedTag;
    // adds nothing of its own.
}
```

The [`@returns`](returns-tag.md) misspelling is also recognized — a
fairly common typo in code — and it parses into the exact same
`ReturnTag` as `@return`.

<note>
PHPStan and Psalm additionally recognize a vendor-prefixed
<code>@phpstan-return</code> / <code>@psalm-return</code> spelling, typically
used to state a more precise type than a plain <code>@return</code> above it
already does. This component parses only the plain <code>@return</code> tag.
</note>

Defined by the [PSR-19 draft `@return`
proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#512-return)
and phpDocumentor's own [`@return`
reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/return.html).
