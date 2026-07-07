# @phan-mixin

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-mixin` tag declares that the members of the referenced
type are magically available on the described class. It is the
vendor-prefixed spelling of [@mixin](mixin-tag.md), which this
component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
