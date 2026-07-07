# @phpstan-immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-immutable` tag declares a class as immutable, defined by
PHPStan. It documents the same concept as this component's own
[@immutable](immutable-tag.md) tag. 

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Originates from PHPStan's own annotation vocabulary.
