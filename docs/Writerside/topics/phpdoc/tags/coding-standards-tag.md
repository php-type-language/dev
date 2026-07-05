# @codingStandards

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@codingStandards` tag names a coding standard for reference. This is
pre-3.2.0 PHP_CodeSniffer-era syntax; current PHP_CodeSniffer prefers the
`phpcs:*` comment syntax instead.

```
"@codingStandards" <Name> [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Associated with PHP_CodeSniffer.
