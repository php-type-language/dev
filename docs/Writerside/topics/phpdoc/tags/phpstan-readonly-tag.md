# @phpstan-readonly

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-readonly` tag declares that a property may only be
written once, during initialization. It covers the same concept as
[@readonly](readonly-tag.md), which this component already
implements bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics)
page confirms PHPStan understands `@readonly`, but has no separate anchor
for this prefixed spelling.
