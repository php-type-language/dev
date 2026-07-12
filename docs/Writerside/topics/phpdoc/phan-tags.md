# Phan Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **Phan Tags** are the annotations that belong to
[Phan](https://github.com/phan/phan)'s own vocabulary, written under its
`@phan-*` vendor prefix. Phan uses the prefix much as Psalm and PHPStan do:
to add analyzer-specific capabilities — line-scoped suppression, real vs.
documented return types, magic-member policing — and to offer a Phan-scoped
override of a tag that also exists in the shared convention.

A prefixed tag that mirrors one documented elsewhere — `@phan-param` beside
[@param](param-tag.md), `@phan-return` beside [@return](return-tag.md) —
means the same thing as the plain spelling; the prefix only marks it as the
one Phan should trust.

## What This Group Covers

* **Assertions** — `@phan-assert`, `@phan-assert-true-condition`,
  `@phan-assert-false-condition`, `@phan-assert-if-true`,
  `@phan-assert-if-false`.
* **Line-scoped suppression** — `@phan-suppress-current-line`,
  `@phan-suppress-next-line`, `@phan-suppress-next-next-line`,
  `@phan-suppress-previous-line`, and the file-wide `@phan-file-suppress`.
* **Return types** — `@phan-real-return`, `@phan-hardcode-return-type`,
  distinguishing the type Phan should infer from the one it should report.
* **Magic-member policy** — `@phan-forbid-undeclared-magic-methods`,
  `@phan-forbid-undeclared-magic-properties`,
  `@phan-read-only`, `@phan-write-only`.
* **Purity and immutability** — `@phan-pure`, `@phan-side-effect-free`,
  `@phan-immutable`, `@phan-constructor-used-for-side-effects`.
* **Prefixed overrides** — `@phan-var`, `@phan-param`, `@phan-return`,
  `@phan-property`, `@phan-method`, `@phan-template`, `@phan-mixin`,
  `@phan-extends`, and the rest, each shadowing its unprefixed counterpart
  for Phan specifically.

<note>
Nearly every tag in this group is recognized once the <b>Phan</b> platform is
enabled. It ships with the library as <code>PhanPlatform</code> and is loaded by
the <a href="platforms.md"><code>DocBlockParser::createDefault()</code></a>
factory:

<code-block lang="php">
$parser = DocBlockParser::createDefault();
</code-block>

Alternatively, pass <code>new PhanPlatform()</code> to the parser constructor to
add it on top of the standard tags. A bare <code>new DocBlockParser()</code>
loads the standard platform only, so an unrecognized <code>@phan-*</code> tag
then falls back to a plain <a href="phpdoc.md#tag">Tag</a>.
</note>

<warning>
Two assertion tags in this group are not yet recognized —
<a href="phan-assert-true-condition-tag.md">@phan-assert-true-condition</a> and
<a href="phan-assert-false-condition-tag.md">@phan-assert-false-condition</a>.
A docblock containing either currently falls back to a plain
<a href="phpdoc.md#tag">Tag</a>. The shorter forms
<code>@phan-assert</code>, <code>@phan-assert-if-true</code> and
<code>@phan-assert-if-false</code> are fully supported.
</warning>

For the authoritative definitions, see
[Phan's annotating-your-source-code guide](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
