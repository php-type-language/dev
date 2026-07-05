# @phpstan-pure-unless-callable-is-impure

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-pure-unless-callable-is-impure` tag is PHPStan's
vendor-specific alias of
[@pure-unless-callable-is-impure](pure-unless-callable-is-impure-tag.md),
which this component already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Like the bare spelling, this one is only weakly sourced — it traces back
to a [phpdoc-parser pull request](https://github.com/phpstan/phpdoc-parser/pull/253)
rather than a PHPStan docs page.
