# @psalm-property-write

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-property-write` tag is Psalm's vendor-specific
`@property-write` alias, the vendor-prefixed spelling of
[@property-write](property-write-tag.md), which this component
already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/).
