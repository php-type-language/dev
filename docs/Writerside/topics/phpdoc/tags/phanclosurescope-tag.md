# @phanclosurescope

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phanclosurescope` tag is an older alias of
[@phan-closure-scope](phan-closure-scope-tag.md), binding the
type of `$this` inside a `Closure`.

This library recognizes `@phanclosurescope` as an alias of the canonical
[@phan-closure-scope](phan-closure-scope-tag.md) tag: it parses identically and produces
the exact same tag instance, keeping the vendor-prefixed name it was
written with.

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
