# @phan-suppress-previous-line

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-suppress-previous-line` tag silences the listed
issue types reported on the previous line.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code);
this specific tag has no dedicated anchor on that page.
