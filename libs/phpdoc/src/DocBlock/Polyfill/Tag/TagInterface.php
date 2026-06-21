<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Polyfill\Tag;

use TypeLang\DocBlock\Description\OptionalDescriptionProviderInterface;

/**
 * @internal polyfill interface for the {@see \TypeLang\DocBlock\Tag\TagInterface}
 *
 * @property-read non-empty-string $name
 */
interface TagInterface extends
    OptionalDescriptionProviderInterface,
    \Stringable {}
