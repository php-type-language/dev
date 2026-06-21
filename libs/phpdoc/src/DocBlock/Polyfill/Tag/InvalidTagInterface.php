<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Polyfill\Tag;

use TypeLang\DocBlock\Tag\TagInterface;

/**
 * @internal polyfill interface for the {@see \TypeLang\DocBlock\Tag\InvalidTagInterface}
 *
 * @property-read \Throwable $reason
 */
interface InvalidTagInterface extends
    TagInterface {}
