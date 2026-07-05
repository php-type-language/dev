# @phan-assert-if-true

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-assert-if-true` tag asserts the given type for an
argument or variable when the function returns `true`. It belongs
to the same assertion family as [@phan-assert](phan-assert-tag.md)
and its counterpart
[@phan-assert-if-false](phan-assert-if-false-tag.md).

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by Phan; see the "Assertions" section of
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
