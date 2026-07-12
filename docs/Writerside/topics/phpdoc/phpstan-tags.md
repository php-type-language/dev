# PHPStan Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **PHPStan Tags** are the annotations that belong to
[PHPStan](https://phpstan.org)'s own vocabulary, written under its
`@phpstan-*` vendor prefix. As with Psalm, the prefix serves two roles: it
introduces PHPStan-specific features — assertions, its `@phpstan-ignore`
family of diagnostic suppressors, type aliases — and it provides a
PHPStan-scoped override of a tag that also exists unprefixed, letting a
project refine a type *for PHPStan only*.

A prefixed tag that mirrors one documented elsewhere — `@phpstan-param`
beside [@param](param-tag.md), `@phpstan-return` beside
[@return](return-tag.md) — carries the same meaning as the plain spelling;
the prefix simply tells PHPStan to prefer this line when it and another tool
disagree.

## What This Group Covers

* **Assertions** — [@phpstan-assert](phpstan-assert-tag.md),
  [@phpstan-assert-if-true](phpstan-assert-if-true-tag.md),
  [@phpstan-assert-if-false](phpstan-assert-if-false-tag.md).
* **Diagnostic suppression** — the `@phpstan-ignore` family
  ([@phpstan-ignore](phpstan-ignore-tag.md),
  [@phpstan-ignore-line](phpstan-ignore-line-tag.md),
  [@phpstan-ignore-next-line](phpstan-ignore-next-line-tag.md)).
* **Purity and immutability** — [@phpstan-pure](phpstan-pure-tag.md),
  [@phpstan-impure](phpstan-impure-tag.md),
  [@phpstan-immutable](phpstan-immutable-tag.md),
  [@phpstan-readonly](phpstan-readonly-tag.md),
  [@phpstan-pure-unless-callable-is-impure](phpstan-pure-unless-callable-is-impure-tag.md).
* **Type aliases and generics** — [@phpstan-type](phpstan-type-tag.md),
  [@phpstan-import-type](phpstan-import-type-tag.md),
  [@phpstan-template](phpstan-template-tag.md) (with its
  [covariant](phpstan-template-covariant-tag.md) and
  [contravariant](phpstan-template-contravariant-tag.md) forms).
* **Prefixed overrides** — [@phpstan-param](phpstan-param-tag.md),
  [@phpstan-return](phpstan-return-tag.md),
  [@phpstan-var](phpstan-var-tag.md),
  [@phpstan-property](phpstan-property-tag.md),
  [@phpstan-method](phpstan-method-tag.md),
  [@phpstan-param-out](phpstan-param-out-tag.md),
  [@phpstan-self-out](phpstan-self-out-tag.md), and the rest, each shadowing
  its unprefixed counterpart for PHPStan specifically.

<note>
Many capabilities PHPStan shares with Psalm are recognized here without a
prefix — see <a href="advanced-tags.md">Advanced Tags</a> for the
vendor-neutral spellings such as
<a href="param-out-tag.md">@param-out</a> and
<a href="pure-unless-callable-is-impure-tag.md">@pure-unless-callable-is-impure</a>.
</note>

<note>
Every tag in this group is recognized once the <b>PHPStan</b> platform is
enabled. It ships with the library as <code>PhpStanPlatform</code> and is loaded
by the <a href="platforms.md"><code>DocBlockParser::createDefault()</code></a>
factory:

<code-block lang="php">
$parser = DocBlockParser::createDefault();
</code-block>

Alternatively, pass <code>new PhpStanPlatform()</code> to the parser constructor
to add it on top of the standard tags. A bare <code>new DocBlockParser()</code>
loads the standard platform only, so an unrecognized <code>@phpstan-*</code> tag
then falls back to a plain <a href="phpdoc.md#tag">Tag</a>.
</note>

For the authoritative definitions, see
[PHPStan's PHPDoc reference](https://phpstan.org/writing-php-code/phpdocs-basics).
