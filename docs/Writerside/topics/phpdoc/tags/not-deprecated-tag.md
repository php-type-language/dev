# @not-deprecated

<primary-label ref="phpdoc-component"/>

The `@not-deprecated` tag marks an element as explicitly not deprecated,
overriding a `@deprecated` tag it would otherwise inherit from a parent
class or interface.

```
"@not-deprecated" [ <Description> ]
```

> A method that stays supported even though the interface it implements is
> deprecated.
> ```php
> /**
>  * @not-deprecated
>  */
> public function send(): void {}
> ```

> With a description explaining why it is kept.
> ```php
> /**
>  * @not-deprecated Still the recommended entry point.
>  */
> ```

Parsing a `@not-deprecated` tag produces a `NotDeprecatedTag` instance.
Being a pure marker, it adds nothing beyond the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already carries.

```php
final class NotDeprecatedTag extends FlagTag {}
```
