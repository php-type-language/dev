# @psalm-stub-override

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-stub-override` tag marks a stub declaration as
intentionally overriding the real signature, telling Psalm not to
flag the mismatch between the two.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by Psalm.
