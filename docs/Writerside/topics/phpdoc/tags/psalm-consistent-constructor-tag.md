# @psalm-consistent-constructor

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-consistent-constructor` tag requires that all subclasses
declare a constructor compatible with the parent's. It is defined
by the static analyzer Psalm.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-consistent-constructor).
