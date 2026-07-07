# @phpstan-require-implements

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-require-implements` tag is PHPStan's vendor-specific
alias of [@require-implements](require-implements-tag.md), which
this component already recognizes bare. It constrains a trait so
that it may only be used within a class that implements the given
interface.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics)
page confirms the concept in prose.
