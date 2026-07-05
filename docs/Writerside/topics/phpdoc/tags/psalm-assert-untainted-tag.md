# @psalm-assert-untainted

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-assert-untainted` tag is defined by the static analyzer
Psalm, part of its assertion family alongside
[@psalm-assert](psalm-assert-tag.md).

```
"@psalm-assert-untainted" <Variable>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>
