# @override

<primary-label ref="phpdoc-component"/>

The `@override` tag marks a method as intentionally overriding a definition
inherited from a parent class. It mirrors PHP 8.3's native `#[\Override]`
attribute, and covers the cases the attribute cannot: codebases that still
support older PHP versions, and interfaces or traits, where the native
attribute has no effect.

```
"@override" [ <Description> ]
```

> Flagging an override on a codebase targeting PHP
> versions before 8.3.
> ```php
> /**
>  * @override
>  */
> public function handle(Request $request): Response
> ```

> With a note about what changed relative to the parent
> implementation.
> ```php
> /**
>  * @override Adds retry handling on top of the
>  *           base transport.
>  */
> public function send(Message $message): bool
> ```

Parsing an `@override` tag produces an `OverrideTag`
instance:

```php
final class OverrideTag extends FlagTag {}
```

Being a pure marker, it adds nothing beyond the `$name`
and optional `$description` every [Tag](phpdoc.md#tag)
already carries.

It resembles PHP 8.3's native `#[\Override]` *attribute* —
and covers the cases that attribute can't, such as codebases
still supporting older PHP versions, or interfaces and
traits, where the native attribute has no effect — but that
attribute is a distinct language feature, not this tag's
origin. Phan has its own, unrelated `@phan-override`. This
tag is this library's own convention.
