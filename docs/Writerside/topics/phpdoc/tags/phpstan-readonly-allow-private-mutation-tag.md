# @phpstan-readonly-allow-private-mutation

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-readonly-allow-private-mutation` tag allows a readonly
property to be mutated from within the declaring class, rather than
only during initialization. It is defined by the static analyzer
PHPStan.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This exact spelling could not be confirmed on PHPStan's own docs, so no
citation link is given here.
