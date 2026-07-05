# @codingStandardsIgnoreStart

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@codingStandardsIgnoreStart` tag starts a block of code excluded
from coding-standard checks, closed by a matching
[@codingStandardsIgnoreEnd](coding-standards-ignore-end-tag.md). This is
pre-3.2.0 PHP_CodeSniffer syntax; current PHP_CodeSniffer prefers the
`phpcs:*` comment syntax instead, but still understands it.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file).
