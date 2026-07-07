# @phan-inherits

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phan-inherits` tag is Phan's alias of `@extends`. It parallels
[@inherits](inherits-tag.md), the equivalent plain-English alias this
component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
