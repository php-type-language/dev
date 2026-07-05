# @phpstan-param-immediately-invoked-callable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-param-immediately-invoked-callable` tag is PHPStan's
vendor-specific spelling of
[@param-immediately-invoked-callable](param-immediately-invoked-callable-tag.md),
which this component already recognizes bare.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#callables).
