# @phan-output-reference

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-output-reference` tag marks a by-reference argument as
output-only — Phan's own counterpart to
[@param-out](param-out-tag.md).

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-output-reference).
