# @phpstan-template-contravariant

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-template-contravariant` tag is PHPStan's
vendor-specific `@template-contravariant` alias. It is the
vendor-prefixed spelling of
[@template-contravariant](template-contravariant-tag.md), which
this component already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

PHPStan's [generics variance](https://phpstan.org/error-identifiers/generics.variance)
page covers contravariance conceptually, though it doesn't spell out this
prefixed tag literally.
