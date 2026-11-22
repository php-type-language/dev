<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Attribute;

use TypeLang\Mapper\Diagnostics\ViolationFormatterInterface;

/**
 * Sets the autoconfigured {@see ViolationFormatterInterface} service as the
 * formatter of the direction in mappers, as the `formatters` key of the
 * config. A mapper takes one formatter per direction: two for the same
 * direction and mapper are an error.
 *
 * ```
 * #[AsMapperFormatter('denormalization')]
 * final class TranslatedViolationFormatter implements ViolationFormatterInterface {}
 * ```
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsMapperFormatter
{
    public function __construct(
        /**
         * Direction whose violations the formatter writes:
         * `denormalization` or `normalization`
         *
         * @var non-empty-string
         */
        public readonly string $direction,
        /**
         * Names of the mappers the service extends, the default mapper and
         * all named ones inheriting it in case of an empty list
         *
         * @var list<non-empty-string>
         */
        public readonly array $mappers = [],
    ) {}
}
