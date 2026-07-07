# @phan-return

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-return` tag is Phan's vendor-specific `@return`
alias, the vendor-prefixed spelling of
[@return](return-tag.md), which this component already
recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
