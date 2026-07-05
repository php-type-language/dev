# @phan-forbid-undeclared-magic-methods

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-forbid-undeclared-magic-methods` tag forbids calling
undeclared magic methods on the class it decorates, so only the
methods actually documented via [@method](method-tag.md) may be
called.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code), where it is mentioned in body text.
