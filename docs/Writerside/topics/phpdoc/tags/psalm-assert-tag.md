# @psalm-assert

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-assert` tag asserts that an argument or variable is
narrowed to a given type after the call returns. It is defined by
the static analyzer Psalm.

```
"@psalm-assert" <Type> <Variable>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
