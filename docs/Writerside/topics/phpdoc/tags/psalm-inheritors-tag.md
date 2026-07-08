# @psalm-inheritors

<primary-label ref="phpdoc-component"/>

The `@psalm-inheritors` tag restricts which classes are allowed to
extend or implement the described type, letting Psalm flag any
subclass or implementation outside the listed set.

```
"@psalm-inheritors" <Type>
```

Parsing a `@psalm-inheritors` tag produces a `PsalmInheritorsTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PsalmInheritorsTag extends TypedTag {}
```

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-inheritors).
