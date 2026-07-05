# @psalm-suppress

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-suppress` tag silences the listed issue types for an
element. It is Psalm's vendor-prefixed spelling of
[@suppress](suppress-tag.md), which this component already
recognizes bare.

```
"@psalm-suppress" <Name> { "," <Name> } [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-suppress-someissuename).
