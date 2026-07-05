# @phpstan-type

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-type` tag declares a local alias for a complex type,
so it can be referenced by name in later `@param`, `@return`, and
`@var` tags. It is defined by the static analyzer PHPStan.

```
"@phpstan-type" <Name> "=" <Type>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See PHPStan's
[PHPDoc types](https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases).
