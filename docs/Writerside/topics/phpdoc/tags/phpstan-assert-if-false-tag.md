# @phpstan-assert-if-false

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-assert-if-false` tag asserts the given type when the
function returns `false`, defined by PHPStan. It is one of the
[@phpstan-assert](phpstan-assert-tag.md) family of narrowing tags.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#narrowing-types-after-function-call).
