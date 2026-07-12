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

* **Assertions** — `@psalm-assert`, `@psalm-assert-if-true`,
  `@psalm-assert-if-false`, `@psalm-assert-untainted`.
* **Immutability and purity** — `@psalm-immutable`, `@psalm-pure`,
  `@psalm-mutation-free`, `@psalm-external-mutation-free`,
  `@psalm-readonly`.
* **Taint analysis** — `@psalm-taint-source`, `@psalm-taint-sink`,
  `@psalm-taint-escape`, `@psalm-taint-specialize`, and related flow tags.
* **Type aliases and generics** — `@psalm-type`, `@psalm-import-type`,
  `@psalm-template` (with its covariant and contravariant forms).
* **Prefixed overrides** — `@psalm-param`, `@psalm-return`, `@psalm-var`,
  `@psalm-property`, `@psalm-method`, and the rest, each shadowing its
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
