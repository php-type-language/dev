# @phpstan-template

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-template` tag is PHPStan's vendor-specific `@template`
alias. It is the vendor-prefixed spelling of
[@template](template-tag.md), which this component already
recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See PHPStan's
[generics guide](https://phpstan.org/blog/generics-in-php-using-phpdocs).
