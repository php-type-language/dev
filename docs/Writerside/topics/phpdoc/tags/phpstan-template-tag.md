# @phpstan-template

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-template` tag is PHPStan's vendor-specific `@template`
alias. It is the vendor-prefixed spelling of
[@template](template-tag.md), which this component already
recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See PHPStan's
[generics guide](https://phpstan.org/blog/generics-in-php-using-phpdocs).
