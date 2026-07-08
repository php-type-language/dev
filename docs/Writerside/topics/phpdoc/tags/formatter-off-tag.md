# @formatter:off

<primary-label ref="phpdoc-component"/>

The `@formatter:off` tag disables PhpStorm's automatic code formatting
from this point on, until a matching
[@formatter:on](formatter-on-tag.md) is found further down the file.

```
"@formatter:off" [ <Description> ]
```

Parsing a `@formatter:off` tag produces a `FormatterOffTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class FormatterOffTag extends FlagTag {}
```

Associated with PhpStorm.
