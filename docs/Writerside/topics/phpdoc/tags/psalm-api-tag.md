# @psalm-api

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-api` tag is Psalm's vendor-specific `@api` alias. It is
the vendor-prefixed spelling of [@api](api-tag.md), which this
component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#api-psalm-api).
