# @psalm-internal

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-internal` tag marks an element as internal to a given
namespace rather than to the whole package, a narrower scope than
this library's already-implemented [@internal](internal-tag.md) tag.

```
"@psalm-internal" [ <Description> ]
```

The namespace Psalm expects after the tag is not parsed separately: it ends
up in `$description`, exactly as it does for `@internal`.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-internal).
