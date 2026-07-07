# @psalm-require-implements

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-require-implements` tag constrains a trait so that it
may only be used within a class that implements the given
interface. It is Psalm's vendor-prefixed spelling of
[@require-implements](require-implements-tag.md), which this
component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-require-implements).
