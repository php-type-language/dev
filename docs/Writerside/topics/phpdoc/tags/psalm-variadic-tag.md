# @psalm-variadic

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-variadic` tag declares that a class's magic
`__call`/`__callStatic` methods accept a variadic list of arguments.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This exact spelling could not be confirmed on Psalm's current
documentation; presented here as attributed to Psalm without a
verified source link.
