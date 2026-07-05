# @psalm-taint-sink

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-taint-sink` tag marks an argument as a taint sink for
the given taint type. It is part of Psalm's taint-analysis
annotations.

```
"@psalm-taint-sink" <Name> <Variable>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/#psalm-taint-sink-lttaint-typegt-ltparam-namegt).
