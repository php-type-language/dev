# @phan-immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-immutable` tag declares a class as immutable. Phan
documents it as an alias of `@phan-read-only` (see
phan-read-only-tag.md); this component's own not-yet-implemented
equivalent for the same concept is
[@immutable](immutable-tag.md).

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
