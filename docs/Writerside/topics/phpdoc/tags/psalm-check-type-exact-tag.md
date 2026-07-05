# @psalm-check-type-exact

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-check-type-exact` tag asserts that a variable resolves
to exactly the given type, disallowing narrower or wider matches.
It is defined by the static analyzer Psalm, alongside the looser
[@psalm-check-type](psalm-check-type-tag.md).

```
"@psalm-check-type-exact" <Variable> "=" <Type>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/) page.
