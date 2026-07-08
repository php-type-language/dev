# @phan-forbid-undeclared-magic-methods

<primary-label ref="phpdoc-component"/>

The `@phan-forbid-undeclared-magic-methods` tag forbids calling
undeclared magic methods on the class it decorates, so only the
methods actually documented via [@method](method-tag.md) may be
called.

```
"@phan-forbid-undeclared-magic-methods" [ <Description> ]
```

Parsing a `@phan-forbid-undeclared-magic-methods` tag produces a `PhanForbidUndeclaredMagicMethodsTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanForbidUndeclaredMagicMethodsTag extends FlagTag {}
```

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code), where it is mentioned in body text.
