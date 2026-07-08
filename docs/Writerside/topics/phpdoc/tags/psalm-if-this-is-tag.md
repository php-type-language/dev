# @psalm-if-this-is

<primary-label ref="phpdoc-component"/>

The `@psalm-if-this-is` tag narrows the type of `$this` inside a
method when the given type matches. It is defined by the static
analyzer Psalm, alongside [@psalm-assert](psalm-assert-tag.md).

```
"@psalm-if-this-is" <Type>
```

Parsing a `@psalm-if-this-is` tag produces a `PsalmIfThisIsTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PsalmIfThisIsTag extends TypedTag {}
```

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
