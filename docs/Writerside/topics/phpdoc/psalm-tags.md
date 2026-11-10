# Psalm Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **Psalm Tags** are the annotations that belong to
[Psalm](https://psalm.dev)'s own vocabulary, written under its `@psalm-*`
vendor prefix. Psalm uses the prefix for two purposes: to introduce
capabilities the shared PHPDoc convention never had — taint tracking,
assertions, mutation freedom — and to offer a Psalm-scoped override of a tag
that also exists unprefixed, so that a project can tighten a type *for Psalm
only* without affecting other tools.

Where a Psalm tag is simply the prefixed twin of a tag already documented
elsewhere — `@psalm-param` beside [@param](param-tag.md),
`@psalm-return` beside [@return](return-tag.md) — the two carry the same
meaning; the prefix only says "trust this spelling over the plain one when
Psalm is the analyzer reading it."

## What This Group Covers

The tags here span Psalm's full feature surface, including:

* **Assertions** — [@psalm-assert](psalm-assert-tag.md),
  [@psalm-assert-if-true](psalm-assert-if-true-tag.md),
  [@psalm-assert-if-false](psalm-assert-if-false-tag.md),
  [@psalm-assert-untainted](psalm-assert-untainted-tag.md).
* **Immutability and purity** — [@psalm-immutable](psalm-immutable-tag.md),
  [@psalm-pure](psalm-pure-tag.md),
  [@psalm-mutation-free](psalm-mutation-free-tag.md),
  [@psalm-external-mutation-free](psalm-external-mutation-free-tag.md),
  [@psalm-readonly](psalm-readonly-tag.md).
* **Taint analysis** — [@psalm-taint-source](psalm-taint-source-tag.md),
  [@psalm-taint-sink](psalm-taint-sink-tag.md),
  [@psalm-taint-escape](psalm-taint-escape-tag.md),
  [@psalm-taint-specialize](psalm-taint-specialize-tag.md), and related
  [flow tags](psalm-flow-tag.md).
* **Type checks and context** —
  [@psalm-check-type](psalm-check-type-tag.md),
  [@psalm-check-type-exact](psalm-check-type-exact-tag.md),
  [@psalm-if-this-is](psalm-if-this-is-tag.md),
  [@psalm-self-out](psalm-self-out-tag.md),
  [@psalm-scope-this](psalm-scope-this-tag.md),
  [@psalm-trace](psalm-trace-tag.md),
  [@psalm-variadic](psalm-variadic-tag.md),
  [@psalm-yield](psalm-yield-tag.md).
* **Class design** — [@psalm-inheritors](psalm-inheritors-tag.md),
  [@psalm-consistent-constructor](psalm-consistent-constructor-tag.md),
  [@psalm-consistent-templates](psalm-consistent-templates-tag.md),
  [@psalm-no-seal-methods](psalm-no-seal-methods-tag.md),
  [@psalm-no-seal-properties](psalm-no-seal-properties-tag.md),
  [@psalm-allow-private-mutation](psalm-allow-private-mutation-tag.md),
  [@psalm-readonly-allow-private-mutation](psalm-readonly-allow-private-mutation-tag.md),
  [@psalm-override-method-visibility](psalm-override-method-visibility-tag.md),
  [@psalm-override-property-visibility](psalm-override-property-visibility-tag.md),
  [@psalm-stub-override](psalm-stub-override-tag.md).
* **Ignoring nullable and falsable results** —
  [@psalm-ignore-nullable-return](psalm-ignore-nullable-return-tag.md),
  [@psalm-ignore-falsable-return](psalm-ignore-falsable-return-tag.md),
  [@psalm-ignore-var](psalm-ignore-var-tag.md),
  [@psalm-ignore-variable-method](psalm-ignore-variable-method-tag.md),
  [@psalm-ignore-variable-property](psalm-ignore-variable-property-tag.md).
* **Type aliases and generics** — [@psalm-type](psalm-type-tag.md),
  [@psalm-import-type](psalm-import-type-tag.md),
  [@psalm-template](psalm-template-tag.md) (with its
  [covariant](psalm-template-covariant-tag.md) and
  [contravariant](psalm-template-contravariant-tag.md) forms).
* **Prefixed overrides** — [@psalm-param](psalm-param-tag.md),
  [@psalm-return](psalm-return-tag.md), [@psalm-var](psalm-var-tag.md),
  [@psalm-property](psalm-property-tag.md),
  [@psalm-method](psalm-method-tag.md), and the rest, each shadowing its
  unprefixed counterpart for Psalm specifically.

<note>
Some of the plainer capabilities Psalm pioneered are recognized here without
the prefix — see <a href="advanced-tags.md">Advanced Tags</a> for the
vendor-neutral spellings such as <a href="suppress-tag.md">@suppress</a> and
<a href="param-out-tag.md">@param-out</a>.
</note>

<note>
Every tag in this group is recognized once the <b>Psalm</b> platform is enabled.
It ships with the library as <code>PsalmPlatform</code> and is loaded by the
<a href="platforms.md"><code>DocBlockParser::createDefault()</code></a> factory:

<code-block lang="php">
$parser = DocBlockParser::createDefault();
</code-block>

Alternatively, pass <code>new PsalmPlatform()</code> to the parser constructor to
add it on top of the standard tags. A bare <code>new DocBlockParser()</code>
loads the standard platform only, so an unrecognized <code>@psalm-*</code> tag
then falls back to a plain <a href="phpdoc.md#tag">Tag</a>.
</note>

For the authoritative definitions, see
[Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/).
