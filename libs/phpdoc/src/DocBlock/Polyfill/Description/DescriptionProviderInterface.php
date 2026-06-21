<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Polyfill\Description;

use TypeLang\DocBlock\Description\DescriptionInterface;
use TypeLang\DocBlock\Description\OptionalDescriptionProviderInterface;

/**
 * @internal polyfill interface for the {@see \TypeLang\DocBlock\Description\DescriptionProviderInterface}
 *
 * @property-read DescriptionInterface $description
 */
interface DescriptionProviderInterface extends
    OptionalDescriptionProviderInterface {}
