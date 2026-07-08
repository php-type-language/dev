# @psalm-self-out

<primary-label ref="phpdoc-component"/>

The `@psalm-self-out` tag documents the refined type of `$this`
after the method call, letting callers see a narrower object type
than the one they started with.

```
"@psalm-self-out" <Type> [ <Description> ]
```

Parsing a `@psalm-self-out` tag produces a `SelfOutTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class SelfOutTag extends TypedTag {}
```

The same `SelfOutTag` is produced by [@phpstan-self-out](phpstan-self-out-tag.md), which restates the same concept for its tool.

The confirmed spelling for the same feature under Psalm's own
documentation is [@psalm-this-out](psalm-this-out-tag.md).
