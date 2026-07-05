# @phpstan-readonly

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-readonly` tag declares that a property may only be
written once, during initialization. It covers the same concept as
[@readonly](readonly-tag.md), which this component already
implements bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics)
page confirms PHPStan understands `@readonly`, but has no separate anchor
for this prefixed spelling.
