# @formatter:on

<primary-label ref="phpdoc-component"/>

The `@formatter:on` tag re-enables PhpStorm's automatic code formatting
from this point on, pairing with an earlier
[@formatter:off](formatter-off-tag.md).

```
"@formatter:on" [ <Description> ]
```

Parsing a `@formatter:on` tag produces a `FormatterOnTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class FormatterOnTag extends FlagTag {}
```

Associated with PhpStorm.
