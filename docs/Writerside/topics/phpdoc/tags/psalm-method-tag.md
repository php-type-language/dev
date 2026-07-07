# @psalm-method

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-method` tag is Psalm's vendor-prefixed spelling of
[@method](method-tag.md), which this library already recognizes bare,
declaring a magic method available on the annotated class.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/).
