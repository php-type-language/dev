# @immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@immutable` tag declares a class as immutable, meaning none of its
state can change after construction. It is listed among this component's
own Standard-category tags, though real enforcement exists independently
in Psalm (`@psalm-immutable`), PHPStan (`@phpstan-immutable`) and Phan
(`@phan-immutable`).

```
"@immutable" [ <Description> ]
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Planned as a Standard-category tag of this component.
