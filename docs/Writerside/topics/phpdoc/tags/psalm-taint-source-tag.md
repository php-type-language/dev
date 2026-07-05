# @psalm-taint-source

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-taint-source` tag marks the return value as a taint
source of the given type. It is part of Psalm's taint-analysis
annotations.

```
"@psalm-taint-source" <Name>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/#psalm-taint-source-lttaint-typegt).
