# @id

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@id` tag assigns a unique identifier to a tutorial section, giving a
[@tutorial](tutorial-tag.md) tag elsewhere a target to link into.

```
"@id" <ID> [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Part of phpDocumentor's original tutorial-authoring tag set.
