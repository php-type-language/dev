# @phpstan-use

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-use` tag is an alias of `@template-use`. It is the
vendor-prefixed spelling of [@use](use-tag.md), more commonly seen
as [@template-use](template-use-tag.md), which this component
already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics) page.
