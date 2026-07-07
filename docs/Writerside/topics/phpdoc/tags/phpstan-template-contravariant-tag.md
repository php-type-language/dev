# @phpstan-template-contravariant

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-template-contravariant` tag is PHPStan's
vendor-specific `@template-contravariant` alias. It is the
vendor-prefixed spelling of
[@template-contravariant](template-contravariant-tag.md), which
this component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

PHPStan's [generics variance](https://phpstan.org/error-identifiers/generics.variance)
page covers contravariance conceptually, though it doesn't spell out this
prefixed tag literally.
