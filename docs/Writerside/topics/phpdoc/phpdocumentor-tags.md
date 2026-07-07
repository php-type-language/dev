# phpDocumentor Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **phpDocumentor Tags** are the tags that originate with
[phpDocumentor](https://www.phpdoc.org) itself — the documentation
generator that defined the original PHPDoc convention long before the
current generation of static analyzers existed. Where the
[Standard](standard-tags.md) and [Advanced](advanced-tags.md) groups collect
tags about *types*, this group collects tags about *documentation*:
authorship, licensing, packaging, cross-references, and the prose that ends
up in a generated API manual.

Many of these predate modern type analysis and carry no type information at
all — they annotate the human-facing documentation rather than constrain the
code. They are grouped here by provenance, and a fair number are legacy
entries from phpDocumentor 1.x kept for compatibility with older docblocks.

## What Belongs Here

* **Authorship and legal** — [@author](author-tag.md),
  [@copyright](copyright-tag.md), [@license](license-tag.md).
* **Packaging and structure** — [@package](package-tag.md),
  [@subpackage](subpackage-tag.md), [@category](category-tag.md),
  [@name](name-tag.md), [@global](global-tag.md),
  [@staticvar](staticvar-tag.md), and the plain [@static](static-tag.md).
* **Cross-references** — [@uses](uses-tag.md) and its reverse
  [@used-by](used-by-tag.md).
* **Versioning and lifecycle** — [@version](version-tag.md) records an
  element's own version, [@since](since-tag.md) the version it first
  appeared in, and [@todo](todo-tag.md) notes work still to be done.
* **Documentation control** — [@inheritdoc](inheritdoc-tag.md) pulls
  documentation down from a parent, [@ignore](ignore-tag.md) hides an
  element from the generated output, and [@access](access-tag.md) is the
  legacy way of recording visibility.
* **Examples and sources** — [@example](example-tag.md),
  [@source](source-tag.md), and [@filesource](filesource-tag.md) embed or
  point to code in the rendered documentation.

<note>
Several of these tags — <a href="access-tag.md">@access</a>,
<a href="staticvar-tag.md">@staticvar</a>, <a href="name-tag.md">@name</a> —
belong to the classic phpDocumentor 1.x vocabulary and have been dropped from
phpDocumentor 3's current reference. They are still parsed here so that
docblocks written against the older toolchain keep working.
</note>

The implemented tags in this group each parse into a dedicated object; a few
entries (such as [@id](id-tag.md), [@toc](toc-tag.md), and
[@tutorial](tutorial-tag.md)) are still marked work-in-progress. Open any
entry in the sidebar for its exact grammar and behavior.
