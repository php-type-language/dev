# Advanced Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **Advanced Tags** are a small set of tags that were originally
introduced by individual static analyzers — chiefly Psalm and PHPStan — to
express things the classic phpDocumentor vocabulary never covered: purity,
by-reference output parameters, how a closure is invoked, whether a class
may grow magic members. Over time these spellings became common enough that
both analyzers came to accept them **without a vendor prefix**, and that is
exactly how they are grouped here.

In other words, this is the vendor-neutral middle ground: less universal
than the [Standard Tags](standard-tags.md), but no longer tied to a single
tool's `@psalm-*` or `@phpstan-*` namespace either. You write
`@param-out`, not `@psalm-param-out`; `@pure-unless-callable-is-impure`, not
its prefixed form.

## What Belongs Here

* **Purity and side effects** —
  [@pure-unless-callable-is-impure](pure-unless-callable-is-impure-tag.md),
  which marks a function pure except when a callable it is handed is itself
  impure.
* **By-reference output** — [@param-out](param-out-tag.md), describing the
  type a parameter *holds after* the call rather than the type it accepts.
* **How a callable is invoked** —
  [@param-closure-this](param-closure-this-tag.md) binds the `$this` a
  closure parameter runs against, while
  [@param-immediately-invoked-callable](param-immediately-invoked-callable-tag.md)
  and
  [@param-later-invoked-callable](param-later-invoked-callable-tag.md)
  distinguish a callback called before the function returns from one stored
  for later.
* **Sealing magic members** — [@seal-methods](seal-methods-tag.md) and
  [@seal-properties](seal-properties-tag.md) forbid a class from resolving
  any magic member not explicitly declared.
* **Diagnostics** — [@suppress](suppress-tag.md) silences named issues on an
  element, and [@unused-param](unused-param-tag.md) marks a parameter kept
  only for signature compatibility.
* **Calling conventions** —
  [@no-named-arguments](no-named-arguments-tag.md) declares that a function
  must not be called with named arguments, and
  [@not-deprecated](not-deprecated-tag.md) asserts an element is explicitly
  *not* deprecated despite what surrounding code might suggest.

<note>
Each of these tags still has a prefixed twin under a specific analyzer —
<code>@psalm-param-out</code>, <code>@phpstan-pure</code>, and so on — listed
under that analyzer's own group in the sidebar. The bare spellings collected
here are the ones both major analyzers agree to read without a prefix.
</note>

Like the standard set, every tag in this group is fully recognized: it
parses into a dedicated, typed object, with any type it carries read through
the same [TypeLang grammar](introduction.md) used everywhere else. Open any
entry in the sidebar for its grammar, resulting object, and examples.
