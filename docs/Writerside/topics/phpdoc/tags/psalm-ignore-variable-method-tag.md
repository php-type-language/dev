# @psalm-ignore-variable-method

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-ignore-variable-method` tag suppresses "undefined method"
issues for methods called on the annotated variable, letting Psalm
skip verification of a magic or dynamically resolved call.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>
