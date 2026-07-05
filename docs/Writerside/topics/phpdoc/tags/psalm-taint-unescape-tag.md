# @psalm-taint-unescape

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-taint-unescape` tag marks a value as tainted again after
passing through the described element, reversing an earlier
[@psalm-taint-escape](psalm-taint-escape-tag.md). It is part of
Psalm's taint-analysis annotations.

```
"@psalm-taint-unescape" <Name>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/).
