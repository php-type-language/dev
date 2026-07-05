# @noinspection

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@noinspection` tag suppresses one or more named IDE inspections for
the element that follows it, so PhpStorm stops flagging a specific,
intentional pattern in that spot.

```
"@noinspection" <Name> [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [PhpStorm](https://www.jetbrains.com/help/phpstorm/disabling-and-enabling-inspections.html).
