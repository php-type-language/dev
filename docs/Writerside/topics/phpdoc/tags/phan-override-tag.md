# @phan-override

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-override` tag is Phan's vendor-specific `@override`
alias, unrelated in origin to this component's own
[@override](override-tag.md): `@phan-override` is a real,
independently documented Phan tag.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
