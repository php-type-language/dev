# @psalm-ignore-falsable-return

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-ignore-falsable-return` tag tells Psalm to ignore a
possible `false` value in the return type. It is defined by the
static analyzer Psalm.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>
