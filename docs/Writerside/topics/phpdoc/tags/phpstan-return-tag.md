# @phpstan-return

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-return` tag is PHPStan's vendor-specific `@return`
alias. It is the vendor-prefixed spelling of
[@return](return-tag.md), which this component already recognizes
bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics) page.
