# @phan-unused-param

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phan-unused-param` tag marks an argument that is
intentionally left unused, similar to
[@unused-param](unused-param-tag.md), which this component
already recognizes bare (itself with no confirmed upstream
origin of its own).

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Defined by Phan, a static analyzer for PHP; no dedicated
documentation page could be confirmed for this tag.
