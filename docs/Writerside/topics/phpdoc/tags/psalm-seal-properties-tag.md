# @psalm-seal-properties

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-seal-properties` tag forbids declaring magic properties
beyond those already documented on the class. It is Psalm's
vendor-prefixed spelling of
[@seal-properties](seal-properties-tag.md), which this component
already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-seal-properties-psalm-no-seal-properties-seal-properties-no-seal-properties).
