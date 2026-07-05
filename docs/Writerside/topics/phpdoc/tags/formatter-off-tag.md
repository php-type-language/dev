# @formatter:off

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@formatter:off` tag disables PhpStorm's automatic code formatting
from this point on, until a matching
[@formatter:on](formatter-on-tag.md) is found further down the file.

```
"@formatter:off"
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Associated with PhpStorm.
