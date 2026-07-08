# @psalm-this-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-this-out` tag documents the refined type of `$this`
after a method call. It is an alias of
[@psalm-self-out](psalm-self-out-tag.md), and is defined by the
static analyzer Psalm.

This library recognizes `@psalm-this-out` as an alias of the canonical
[@psalm-self-out](psalm-self-out-tag.md) tag: it parses identically and produces
the exact same tag instance, keeping the vendor-prefixed name it was
written with.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
