# @psalm-override-property-visibility

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-override-property-visibility` tag allows a subclass to
override an inherited property with a different visibility than the
parent declares, without Psalm flagging the change as a violation.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

This exact tag name could not be confirmed on Psalm's own
annotations documentation, so no citation link is given here.
