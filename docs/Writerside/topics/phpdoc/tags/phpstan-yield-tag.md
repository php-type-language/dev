# @phpstan-yield

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-yield` tag documents the type yielded by a
`Generator`, distinct from the type it returns on completion. It is
defined by the static analyzer PHPStan.

```
"@phpstan-yield" <Type> [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics) page.
