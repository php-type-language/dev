# @psalm-readonly-allow-private-mutation

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-readonly-allow-private-mutation` tag allows a readonly
property to be mutated from within the declaring class, relaxing
the usual [@psalm-readonly](psalm-readonly-tag.md) constraint for
internal use only.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/).
