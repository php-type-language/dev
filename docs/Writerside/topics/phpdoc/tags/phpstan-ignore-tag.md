# @phpstan-ignore

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-ignore` tag silences the listed error identifiers reported
on the current line, defined by PHPStan.

```
"@phpstan-ignore" <Name> { "," <Name> }
    [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Documented by
[PHPStan](https://phpstan.org/user-guide/ignoring-errors#ignoring-in-code-using-phpdocs).
