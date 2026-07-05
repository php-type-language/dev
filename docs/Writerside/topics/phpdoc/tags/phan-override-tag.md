# @phan-override

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-override` tag is Phan's vendor-specific `@override`
alias, unrelated in origin to this component's own
[@override](override-tag.md): unlike that tag, whose bare spelling
has no confirmed third-party source (see that page), `@phan-override`
is a real Phan-defined tag.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This prefixed spelling has no dedicated anchor on
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
