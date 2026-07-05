# @phanclosurescope

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phanclosurescope` tag is an older alias of
[@phan-closure-scope](phan-closure-scope-tag.md), binding the
type of `$this` inside a `Closure`.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
