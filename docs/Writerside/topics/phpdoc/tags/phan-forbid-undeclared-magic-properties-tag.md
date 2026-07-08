# @phan-forbid-undeclared-magic-properties

<primary-label ref="phpdoc-component"/>

The `@phan-forbid-undeclared-magic-properties` tag forbids accessing
undeclared magic properties on the class it decorates, so only the
properties actually documented via
[@property](property-tag.md) may be accessed.

```
"@phan-forbid-undeclared-magic-properties" [ <Description> ]
```

Parsing a `@phan-forbid-undeclared-magic-properties` tag produces a `PhanForbidUndeclaredMagicPropertiesTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanForbidUndeclaredMagicPropertiesTag extends FlagTag {}
```

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code), where it is mentioned in body text.
