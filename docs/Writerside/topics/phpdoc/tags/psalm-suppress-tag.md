# @psalm-suppress

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@psalm-suppress` tag silences the listed issue types for an
element. It is Psalm's vendor-prefixed spelling of
[@suppress](suppress-tag.md), which this component already
recognizes bare.

```
"@psalm-suppress" <Name> { "," <Name> } [ <Description> ]
```

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-suppress-someissuename).
