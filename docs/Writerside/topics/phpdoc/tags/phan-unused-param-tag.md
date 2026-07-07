# @phan-unused-param

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-unused-param` tag marks an argument that is
intentionally left unused, similar to
[@unused-param](unused-param-tag.md), which this component
already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by Phan, a static analyzer for PHP.
