# @phan-suppress-next-line

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-suppress-next-line` tag silences the listed issue
types reported on the next line, mirroring
[@phan-suppress-current-line](phan-suppress-current-line-tag.md)
but targeting the line that follows.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-suppress-next-line).
