# @psalm-param-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-param-out` tag documents the type that a by-reference
argument holds after the call completes. It is Psalm's
vendor-prefixed spelling of [@param-out](param-out-tag.md), which
this component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#param-out-psalm-param-out).
