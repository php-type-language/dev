# @psalm-internal

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-internal` tag marks an element as internal to a given
namespace rather than to the whole package, a narrower scope than
this library's already-implemented [@internal](internal-tag.md) tag.

```
"@psalm-internal" [ <Name> ] [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-internal).
