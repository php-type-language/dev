# @psalm-scope-this

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-scope-this` tag binds the type of `$this` inside a
`Closure`, letting Psalm type-check the closure body as if it were
a method of the given class.

```
"@psalm-scope-this" <Type>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>
