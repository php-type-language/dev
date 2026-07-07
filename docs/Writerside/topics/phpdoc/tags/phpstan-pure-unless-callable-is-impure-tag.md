# @phpstan-pure-unless-callable-is-impure

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-pure-unless-callable-is-impure` tag is PHPStan's
vendor-specific alias of
[@pure-unless-callable-is-impure](pure-unless-callable-is-impure-tag.md),
which this component already recognizes bare.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Like the bare spelling, this one is only weakly sourced — it traces back
to a [phpdoc-parser pull request](https://github.com/phpstan/phpdoc-parser/pull/253)
rather than a PHPStan docs page.
