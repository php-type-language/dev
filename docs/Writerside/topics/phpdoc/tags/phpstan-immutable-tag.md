# @phpstan-immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-immutable` tag declares a class as immutable, defined by
PHPStan. It documents the same concept as this component's own
[@immutable](immutable-tag.md) tag. 

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Originates from PHPStan's own annotation vocabulary.
