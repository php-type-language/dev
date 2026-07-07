# @immutable

<primary-label ref="phpdoc-component"/>

The `@immutable` tag declares a class as immutable, meaning none of its
state can change after construction. Real enforcement exists independently
in Psalm (`@psalm-immutable`), PHPStan (`@phpstan-immutable`) and Phan
(`@phan-immutable`).

```
"@immutable" [ <Description> ]
```

> A value object whose state is fixed once built.
> ```php
> /**
>  * @immutable
>  */
> final class Money {}
> ```

> With a description explaining the guarantee.
> ```php
> /**
>  * @immutable Every "with*" method returns a new instance.
>  */
> ```

Parsing an `@immutable` tag produces an `ImmutableTag` instance. Being a
pure marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class ImmutableTag extends FlagTag {}
```
