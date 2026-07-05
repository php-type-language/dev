# @psalm-use

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-use` tag is Psalm's vendor-specific `@use` alias. It is
the vendor-prefixed spelling of [@use](use-tag.md), more commonly
seen under its alias [@template-use](template-use-tag.md), both of
which this component already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/).
