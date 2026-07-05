# @phan-closure-scope

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-closure-scope` tag binds the type of `$this` inside a
`Closure`, letting Phan analyze the closure's body as though it were
bound to an instance of the given class.

```
"@phan-closure-scope" <Type>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-closure-scope).
