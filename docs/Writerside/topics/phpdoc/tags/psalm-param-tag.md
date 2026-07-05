# @psalm-param

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-param` tag is Psalm's vendor-prefixed spelling of
[@param](param-tag.md), which this library already recognizes bare,
documenting the type of a function or method parameter.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This spelling's own anchor could not be confirmed on [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/).
