# @phan-inherits

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-inherits` tag is Phan's alias of `@extends`. It parallels
[@inherits](inherits-tag.md), the equivalent plain-English alias this
component already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This tag has no dedicated anchor on
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
