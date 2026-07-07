# Standard Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **Standard Tags** are the everyday PHPDoc vocabulary — the handful of
tags nearly every PHP codebase already uses, and that every major toolchain
(phpDocumentor, PHPStan, Psalm, PhpStorm) understands without any
configuration. If a docblock carries type information at all, it is almost
certainly through one of the tags on this list.

They are grouped here not by which tool invented them, but by the fact that
they need no vendor prefix: `@param`, not `@psalm-param`; `@return`, not
`@phpstan-return`. This is the common ground the entire ecosystem shares.

## What Belongs Here

The set breaks down into a few families:

* **Signatures and types** — the tags that describe what a member accepts,
  returns, and holds: [@param](param-tag.md), [@return](return-tag.md)
  (with its alias [@returns](returns-tag.md)), and [@var](var-tag.md).
* **Magic members** — declaring members that have no native PHP
  declaration to attach to: [@method](method-tag.md),
  [@property](property-tag.md), [@property-read](property-read-tag.md), and
  [@property-write](property-write-tag.md).
* **Generics** — turning a plain class into a generic one and wiring its
  type parameters through inheritance:
  [@template](template-tag.md) and its variance-annotated forms
  ([@template-covariant](template-covariant-tag.md),
  [@template-contravariant](template-contravariant-tag.md),
  [@template-invariant](template-invariant-tag.md)),
  together with [@extends](extends-tag.md) /
  [@template-extends](template-extends-tag.md) / [@inherits](inherits-tag.md),
  [@implements](implements-tag.md) /
  [@template-implements](template-implements-tag.md), and
  [@use](use-tag.md) / [@template-use](template-use-tag.md).
* **Modifiers and markers** — docblock stand-ins for language keywords and
  intent: [@abstract](abstract-tag.md), [@final](final-tag.md),
  [@readonly](readonly-tag.md), [@immutable](immutable-tag.md),
  [@override](override-tag.md), [@mixin](mixin-tag.md), and the
  visibility boundary pair [@api](api-tag.md) /
  [@internal](internal-tag.md).
* **Constraints** — narrowing where a trait or interface may be used:
  [@require-extends](require-extends-tag.md) and
  [@require-implements](require-implements-tag.md).
* **Exceptions** — documenting what PHP has no native syntax for:
  [@throws](throws-tag.md) (with its alias [@throw](throw-tag.md)).
* **References and metadata** — [@see](see-tag.md), [@link](link-tag.md),
  [@deprecated](deprecated-tag.md).

<tip>
Several entries here are aliases of one another —
<a href="return-tag.md">@return</a> / <a href="returns-tag.md">@returns</a>,
<a href="throws-tag.md">@throws</a> / <a href="throw-tag.md">@throw</a>. Both
spellings are recognized and parse into the same tag object.
</tip>

Every tag in this group is fully recognized by the parser: it produces a
dedicated, typed tag object, and any type it carries is parsed with the same
[TypeLang grammar](introduction.md) used throughout the project. Pick any
entry in the sidebar for its exact grammar, the object it produces, and
worked examples.
