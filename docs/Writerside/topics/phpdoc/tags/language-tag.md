# @language

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@language` tag injects a foreign-language grammar — SQL, HTML,
regular expressions, and the like — into a string literal, so PhpStorm can
apply the right syntax highlighting and completion inside it.

```
"@language" <Name>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Associated with PhpStorm; no dedicated JetBrains page documenting this
exact doc-comment tag could be confirmed.
