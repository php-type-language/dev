# @psalm-this-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-this-out` tag documents the refined type of `$this`
after a method call. It is an alias of
[@psalm-self-out](psalm-self-out-tag.md), and is defined by the
static analyzer Psalm.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
