# @psalm-readonly

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-readonly` tag declares that a property may only be
written once, during initialization. It covers the same concept as
[@readonly](readonly-tag.md), which this component already
implements bare and which PHPStan also independently supports.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-readonly-and-readonly).
