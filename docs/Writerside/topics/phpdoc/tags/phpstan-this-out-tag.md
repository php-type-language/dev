# @phpstan-this-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-this-out` tag is an alias of `@phpstan-self-out` (see
[phpstan-self-out-tag.md](phpstan-self-out-tag.md)). It documents
the refined type of `$this` after a method call.

This library recognizes `@phpstan-this-out` as an alias of the canonical
[@phpstan-self-out](phpstan-self-out-tag.md) tag: it parses identically and produces
the exact same tag instance, keeping the vendor-prefixed name it was
written with.

See PHPStan's
[PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics#change-type-of-current-object-after-calling-a-method).
