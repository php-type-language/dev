# @phpstan-template-covariant

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-template-covariant` tag is PHPStan's vendor-specific
`@template-covariant` alias. It is the vendor-prefixed spelling of
[@template-covariant](template-covariant-tag.md), which this
component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

See PHPStan's
[covariant templates post](https://phpstan.org/blog/whats-up-with-template-covariant).
