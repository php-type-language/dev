# @phpstan-param-closure-this

<primary-label ref="phpdoc-component"/>
<secondary-label ref="alias-tag"/>

The `@phpstan-param-closure-this` tag is PHPStan's vendor-specific
spelling of [@param-closure-this](param-closure-this-tag.md), which this
component already recognizes bare — documenting what `$this` refers to
inside a `Closure` parameter.

This library recognizes it as an alias: it parses identically to the
canonical tag above and produces the exact same tag instance, keeping the
vendor-prefixed name it was written with.

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#callables).
