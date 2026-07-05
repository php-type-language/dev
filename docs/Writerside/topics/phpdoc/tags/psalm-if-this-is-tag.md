# @psalm-if-this-is

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-if-this-is` tag narrows the type of `$this` inside a
method when the given type matches. It is defined by the static
analyzer Psalm, alongside [@psalm-assert](psalm-assert-tag.md).

```
"@psalm-if-this-is" <Type>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
