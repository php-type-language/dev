# @psalm-trace

<primary-label ref="phpdoc-component"/>

The `@psalm-trace` tag outputs the inferred type of the given variable, for
debugging purposes.

```
"@psalm-trace" <Variable> [ <Description> ]
```

> Inspecting the type Psalm infers for a variable.
> ```php
> /**
>  * @psalm-trace $result
>  */
> ```

Parsing a `@psalm-trace` tag produces a `PsalmTraceTag` instance, carrying
the traced `$variable` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmTraceTag extends VariableTag {}
```

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-trace).
