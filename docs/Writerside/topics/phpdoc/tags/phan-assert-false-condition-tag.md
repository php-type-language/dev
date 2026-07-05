# @phan-assert-false-condition

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-assert-false-condition` tag asserts that a given
expression evaluates to `false` at the point of the call. It belongs
to the same assertion family as [@phan-assert](phan-assert-tag.md).

```
"@phan-assert-false-condition" <Expression>
```

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by Phan; see the "Assertions" section of
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
