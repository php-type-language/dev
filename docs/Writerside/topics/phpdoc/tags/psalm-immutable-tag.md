# @psalm-immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-immutable` tag declares a class as immutable. It is
Psalm's real, currently-supported counterpart to this library's own
not-yet-implemented [@immutable](immutable-tag.md) tag, which
documents the same concept in a tool-agnostic way.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-immutable).
