# @psalm-use

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-use` tag is Psalm's vendor-specific `@use` alias. It is
the vendor-prefixed spelling of [@use](use-tag.md), more commonly
seen under its alias [@template-use](template-use-tag.md), both of
which this component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/).
