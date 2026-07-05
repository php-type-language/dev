# @phpstan-assert

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-assert` tag asserts that an argument or variable is
narrowed to a given type after the call, defined by PHPStan. It has
`-if-true` and `-if-false` siblings for assertions that only hold when a
boolean-returning function returns a particular value.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#narrowing-types-after-function-call).
