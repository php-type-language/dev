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

<warning>
The tags in this group are listed for reference only. They are catalogued so
the vocabulary is documented in one place, but are <b>not yet recognized</b>
by the parser — a docblock containing one currently falls back to a plain
<a href="phpdoc.md#tag">Tag</a>. Their pages are marked work-in-progress in
the sidebar.
</warning>

For the authoritative definitions, see
[Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/).
