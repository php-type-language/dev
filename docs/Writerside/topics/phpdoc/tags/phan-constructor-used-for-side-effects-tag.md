# @phan-constructor-used-for-side-effects

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-constructor-used-for-side-effects` tag declares that a
constructor's return value is intentionally discarded by callers,
suppressing the issue Phan would otherwise raise for an unused `new`
expression.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
