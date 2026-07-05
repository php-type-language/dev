# @phan-assert

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-assert` tag asserts that an argument or variable is
narrowed to a given type after the call returns. Phan documents it
together with its `@phan-assert-if-true` and `@phan-assert-if-false`
siblings — see [@phan-assert-if-true](phan-assert-if-true-tag.md) and
[@phan-assert-if-false](phan-assert-if-false-tag.md) — as part of the
same assertion family.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by Phan; see the "Assertions" section of
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code)
(no dedicated anchor for this exact tag).
