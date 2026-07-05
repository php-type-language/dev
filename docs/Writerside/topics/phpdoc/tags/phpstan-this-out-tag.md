# @phpstan-this-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-this-out` tag is an alias of `@phpstan-self-out` (see
[phpstan-self-out-tag.md](phpstan-self-out-tag.md)). It documents
the refined type of `$this` after a method call.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See PHPStan's
[PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics#change-type-of-current-object-after-calling-a-method).
