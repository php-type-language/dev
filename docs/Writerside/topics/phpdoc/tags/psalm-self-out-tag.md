# @psalm-self-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-self-out` tag documents the refined type of `$this`
after the method call, letting callers see a narrower object type
than the one they started with.

```
"@psalm-self-out" <Type> [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This exact spelling could not be confirmed under Psalm's own
documentation, where the confirmed name for the same feature is
`@psalm-this-out`.
