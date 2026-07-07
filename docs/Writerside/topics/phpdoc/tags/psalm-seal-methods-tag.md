# @psalm-seal-methods

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-seal-methods` tag forbids declaring magic methods
beyond those already documented on the class. It is Psalm's
vendor-prefixed spelling of
[@seal-methods](seal-methods-tag.md), which this component already
recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-seal-methods-psalm-no-seal-methods-seal-methods-no-seal-methods).
