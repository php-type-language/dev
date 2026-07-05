# @phpstan-consistent-constructor

<primary-label ref="phpdoc-component"/>
<secondary-label ref="not-implemented"/>

The `@phpstan-consistent-constructor` tag requires that all subclasses
declare a constructor compatible with the parent's, defined by PHPStan.
This exact spelling could not be verified on PHPStan's own writing-php-code
pages, so no link is given here.

<note>
Not yet recognized by <code>TypeLang\PhpDoc\DocBlockParser</code> — parsing
a docblock containing this tag returns a plain <code>Tag</code>, its whole
suffix folded into the description. See
<a href="custom-tags.md">Custom Tags</a> for the current workaround if you
need to recognize it yourself.
</note>

Originates from PHPStan's own annotation vocabulary.
