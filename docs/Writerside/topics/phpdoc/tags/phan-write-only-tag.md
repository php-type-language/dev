# @phan-write-only

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-write-only` tag, paired with
[@phan-read-only](phan-read-only-tag.md), declares that a
property may only ever be written, never read.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-read-only-and-phan-write-only).
