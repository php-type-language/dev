# @psalm-implements

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-implements` tag is Psalm's vendor-prefixed spelling of
[@implements](implements-tag.md), which this library already
recognizes bare, and carries the same meaning: documenting the
concrete types bound to a generic interface's template parameters.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/).
