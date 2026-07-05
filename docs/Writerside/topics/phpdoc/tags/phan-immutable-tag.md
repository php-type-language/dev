# @phan-immutable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-immutable` tag declares a class as immutable. Phan
documents it as an alias of `@phan-read-only` (see
phan-read-only-tag.md); this component's own not-yet-implemented
equivalent for the same concept is
[@immutable](immutable-tag.md).

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This tag has no dedicated anchor on
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
