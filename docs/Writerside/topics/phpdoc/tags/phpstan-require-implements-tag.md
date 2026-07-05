# @phpstan-require-implements

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-require-implements` tag is PHPStan's vendor-specific
alias of [@require-implements](require-implements-tag.md), which
this component already recognizes bare. It constrains a trait so
that it may only be used within a class that implements the given
interface.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics)
page confirms the concept in prose, though it has no dedicated anchor for
this prefixed spelling.
