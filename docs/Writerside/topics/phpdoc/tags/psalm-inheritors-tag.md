# @psalm-inheritors

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-inheritors` tag restricts which classes are allowed to
extend or implement the described type, letting Psalm flag any
subclass or implementation outside the listed set.

```
"@psalm-inheritors" <Type>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-inheritors).
