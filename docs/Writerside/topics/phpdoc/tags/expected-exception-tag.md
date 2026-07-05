# @expectedException

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@expectedException` tag declares the `Throwable` a test method is
expected to throw. It is an old PHPUnit-era convention that predates the
`expectException()` method call and is unrelated to PhpStorm itself.

```
"@expectedException" <Type> [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

A historical PHPUnit convention, not a PhpStorm tag.
