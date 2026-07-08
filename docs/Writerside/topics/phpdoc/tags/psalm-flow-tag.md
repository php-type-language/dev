# @psalm-flow

<primary-label ref="phpdoc-component"/>

The `@psalm-flow` tag is part of Psalm's taint-analysis annotations,
used to describe how tainted data flows through a function's
parameters and return value.

```
"@psalm-flow" <FlowType> [ <Variable> ]
```

The `<FlowType>` is a keyword — one of `TaintSource`, `TaintSink`,
`TaintSpecialize` or `TaintUnescape` — captured into a `FlowType` enum.

Parsing a `@psalm-flow` tag produces a `PsalmFlowTag` instance, carrying the
parsed `$flow` and optional `$variable` alongside the `$name` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PsalmFlowTag extends Tag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/).
