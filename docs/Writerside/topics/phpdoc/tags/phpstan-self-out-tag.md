# @phpstan-self-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-self-out` tag documents the refined type of `$this`
after a method call, letting PHPStan track how a fluent or
mutating method narrows the object's own type. It is defined by the
static analyzer PHPStan, which also accepts the alias
`@phpstan-this-out` (see [phpstan-this-out-tag.md](phpstan-this-out-tag.md)).

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

See PHPStan's
[PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics#change-type-of-current-object-after-calling-a-method).
