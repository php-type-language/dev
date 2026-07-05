# @psalm-seal-properties

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-seal-properties` tag forbids declaring magic properties
beyond those already documented on the class. It is Psalm's
vendor-prefixed spelling of
[@seal-properties](seal-properties-tag.md), which this component
already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-seal-properties-psalm-no-seal-properties-seal-properties-no-seal-properties).
