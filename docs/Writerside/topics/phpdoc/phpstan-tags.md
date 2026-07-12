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

* **Assertions** — `@phpstan-assert`, `@phpstan-assert-if-true`,
  `@phpstan-assert-if-false`.
* **Diagnostic suppression** — the `@phpstan-ignore` family
  (`@phpstan-ignore`, `@phpstan-ignore-line`, `@phpstan-ignore-next-line`).
* **Purity and immutability** — `@phpstan-pure`, `@phpstan-impure`,
  `@phpstan-immutable`, `@phpstan-readonly`,
  `@phpstan-pure-unless-callable-is-impure`.
* **Type aliases and generics** — `@phpstan-type`, `@phpstan-import-type`,
  `@phpstan-template` (with its covariant and contravariant forms).
* **Prefixed overrides** — `@phpstan-param`, `@phpstan-return`,
  `@phpstan-var`, `@phpstan-property`, `@phpstan-method`,
  `@phpstan-param-out`, `@phpstan-self-out`, and the rest, each shadowing
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
