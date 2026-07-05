# @psalm-template-covariant

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-template-covariant` tag is Psalm's vendor-specific
`@template-covariant` alias. It is the vendor-prefixed spelling of
[@template-covariant](template-covariant-tag.md), which this
component already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's templated annotations](https://psalm.dev/docs/annotating_code/templated_annotations/).
