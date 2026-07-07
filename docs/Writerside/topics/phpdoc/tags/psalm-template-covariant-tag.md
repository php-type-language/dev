# @psalm-template-covariant

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-template-covariant` tag is Psalm's vendor-specific
`@template-covariant` alias. It is the vendor-prefixed spelling of
[@template-covariant](template-covariant-tag.md), which this
component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Psalm's templated annotations](https://psalm.dev/docs/annotating_code/templated_annotations/).
