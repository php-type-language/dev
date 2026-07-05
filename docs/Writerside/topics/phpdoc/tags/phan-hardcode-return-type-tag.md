# @phan-hardcode-return-type

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-hardcode-return-type` tag forces Phan to use the
documented return type instead of the type it would otherwise infer
from the method body.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by Phan.
