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

* **Assertions** — [@phan-assert](phan-assert-tag.md),
  [@phan-assert-true-condition](phan-assert-true-condition-tag.md),
  [@phan-assert-false-condition](phan-assert-false-condition-tag.md),
  [@phan-assert-if-true](phan-assert-if-true-tag.md),
  [@phan-assert-if-false](phan-assert-if-false-tag.md).
* **Line-scoped suppression** —
  [@phan-suppress-current-line](phan-suppress-current-line-tag.md),
  [@phan-suppress-next-line](phan-suppress-next-line-tag.md),
  [@phan-suppress-next-next-line](phan-suppress-next-next-line-tag.md),
  [@phan-suppress-previous-line](phan-suppress-previous-line-tag.md), and the
  file-wide [@phan-file-suppress](phan-file-suppress-tag.md).
* **Return types** — [@phan-real-return](phan-real-return-tag.md),
  [@phan-hardcode-return-type](phan-hardcode-return-type-tag.md),
  distinguishing the type Phan should infer from the one it should report.
* **Magic-member policy** —
  [@phan-forbid-undeclared-magic-methods](phan-forbid-undeclared-magic-methods-tag.md),
  [@phan-forbid-undeclared-magic-properties](phan-forbid-undeclared-magic-properties-tag.md),
  [@phan-read-only](phan-read-only-tag.md),
  [@phan-write-only](phan-write-only-tag.md).
* **Purity and immutability** — [@phan-pure](phan-pure-tag.md),
  [@phan-side-effect-free](phan-side-effect-free-tag.md),
  [@phan-immutable](phan-immutable-tag.md),
  [@phan-constructor-used-for-side-effects](phan-constructor-used-for-side-effects-tag.md).
* **Prefixed overrides** — [@phan-var](phan-var-tag.md),
  [@phan-param](phan-param-tag.md), [@phan-return](phan-return-tag.md),
  [@phan-property](phan-property-tag.md),
  [@phan-method](phan-method-tag.md), [@phan-template](phan-template-tag.md),
  [@phan-mixin](phan-mixin-tag.md), [@phan-extends](phan-extends-tag.md), and
  the rest, each shadowing its unprefixed counterpart for Phan specifically.

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
