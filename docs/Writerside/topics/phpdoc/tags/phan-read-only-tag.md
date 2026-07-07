# @phan-read-only

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-read-only` tag, paired with
[@phan-write-only](phan-write-only-tag.md), declares that a
property may only ever be read, or only ever be written.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-read-only-and-phan-write-only).
