# @psalm-ignore-variable-property

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-ignore-variable-property` tag suppresses "undefined
property" issues for properties accessed on the annotated variable,
letting Psalm skip verification of a magic or dynamically resolved
property access.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This exact spelling could not be confirmed on Psalm's own supported
annotations page, so no citation link is given here.
