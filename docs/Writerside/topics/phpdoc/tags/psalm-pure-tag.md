# @psalm-pure

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-pure` tag declares a function or method as pure, meaning
it is free of side effects: calling it repeatedly with the same
arguments always produces the same result, without observably
mutating any state.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-pure).
