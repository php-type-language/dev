# @phan-file-suppress

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-file-suppress` tag silences the listed issue types for
the whole file it appears in, rather than just a single element or
line.

```
"@phan-file-suppress" <Name> { "," <Name> } [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-file-suppress).
