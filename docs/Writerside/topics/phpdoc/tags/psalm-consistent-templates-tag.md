# @psalm-consistent-templates

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@psalm-consistent-templates` tag requires that all subclasses
use the same template parameters as the parent. It is defined by
the static analyzer Psalm.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>
