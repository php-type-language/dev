# @not-deprecated

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@not-deprecated` tag marks an element as explicitly not deprecated,
overriding a `@deprecated` tag it would otherwise inherit from a parent
class or interface.

```
"@not-deprecated" [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Planned as this component's own Advanced-category tag.
