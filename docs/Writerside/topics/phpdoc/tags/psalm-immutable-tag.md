# @psalm-immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-immutable` tag declares a class as immutable. It is
Psalm's real, currently-supported counterpart to this library's own
not-yet-implemented [@immutable](immutable-tag.md) tag, which
documents the same concept in a tool-agnostic way.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-immutable).
