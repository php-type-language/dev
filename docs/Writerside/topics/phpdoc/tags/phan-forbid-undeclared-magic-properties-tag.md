# @phan-forbid-undeclared-magic-properties

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-forbid-undeclared-magic-properties` tag forbids accessing
undeclared magic properties on the class it decorates, so only the
properties actually documented via
[@property](property-tag.md) may be accessed.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This tag has no dedicated anchor on
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code)
— it is mentioned only in body text there.
