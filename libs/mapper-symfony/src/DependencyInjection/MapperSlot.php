<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperCoercer;
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperFormatter;
use TypeLang\Mapper\Coercer\CoercerInterface;
use TypeLang\Mapper\Context\Coercers;
use TypeLang\Mapper\Diagnostics\ViolationFormatterInterface;
use TypeLang\Mapper\Mapper;

/**
 * Role of a service a mapper takes one of per key, such as the coercer of a
 * type. The service of a key of a named mapper replaces the one of the
 * default mapper. The value is the tag of the service.
 *
 * @phpstan-import-type MapperSettingsType from MapperDefinitions
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Symfony
 */
enum MapperSlot: string
{
    case Coercer = 'type_lang_mapper.coercer';
    case Formatter = 'type_lang_mapper.formatter';

    /**
     * Returns the name of the role in messages.
     *
     * @return non-empty-string
     */
    public function getName(): string
    {
        return match ($this) {
            self::Coercer => 'coercer',
            self::Formatter => 'formatter',
        };
    }

    /**
     * Returns the name of the key, the attribute of the tag holding it.
     *
     * @return non-empty-string
     */
    public function getKeyName(): string
    {
        return match ($this) {
            self::Coercer => 'type',
            self::Formatter => 'direction',
        };
    }

    /**
     * Returns the key of the config holding the services by the key.
     *
     * @return non-empty-string
     */
    public function getConfigKey(): string
    {
        return match ($this) {
            self::Coercer => 'coercers',
            self::Formatter => 'formatters',
        };
    }

    /**
     * Returns the description of the key of the config.
     *
     * @return non-empty-string
     */
    public function getConfigInfo(): string
    {
        return match ($this) {
            self::Coercer => 'Coercers of the scalar types and of the array input, by the type',
            self::Formatter => 'Formatters of the messages of the violations, by the direction',
        };
    }

    /**
     * Returns the keys a mapper takes a service of: the types of the
     * arguments of {@see Coercers::createDefault()}, the directions of the
     * formatter arguments of {@see Mapper}.
     *
     * @return non-empty-list<non-empty-string>
     */
    public function getKeys(): array
    {
        return match ($this) {
            self::Coercer => ['int', 'float', 'string', 'bool', 'array'],
            self::Formatter => ['denormalization', 'normalization'],
        };
    }

    /**
     * @return class-string<AsMapperCoercer|AsMapperFormatter>
     */
    public function getAttribute(): string
    {
        return match ($this) {
            self::Coercer => AsMapperCoercer::class,
            self::Formatter => AsMapperFormatter::class,
        };
    }

    /**
     * @return class-string
     */
    public function getInterface(): string
    {
        return match ($this) {
            self::Coercer => CoercerInterface::class,
            self::Formatter => ViolationFormatterInterface::class,
        };
    }

    /**
     * Returns the key the attribute declares the service for.
     */
    public function getAttributeKey(AsMapperCoercer|AsMapperFormatter $attribute): string
    {
        if ($attribute instanceof AsMapperCoercer) {
            return $attribute->type;
        }

        return $attribute->direction;
    }

    /**
     * Returns the services given in the settings of a mapper by the key,
     * without the inherited ones.
     *
     * @param MapperSettingsType $settings
     * @return array<non-empty-string, non-empty-string>
     */
    public function getConfiguredServices(array $settings): array
    {
        $configured = match ($this) {
            self::Coercer => $settings['coercers'],
            self::Formatter => $settings['formatters'],
        };

        $result = [];

        foreach ($this->getKeys() as $key) {
            $service = $configured[$key] ?? null;

            if ($service !== null) {
                $result[$key] = $service;
            }
        }

        return $result;
    }

    /**
     * Passes the services of the mapper by the key to its definition.
     *
     * @param array<non-empty-string, Reference> $references
     */
    public function setServices(Definition $mapper, array $references): void
    {
        if ($this === self::Coercer) {
            $mapper->setArgument('$coercers', $references);

            return;
        }

        foreach ($references as $direction => $reference) {
            $mapper->setArgument('$' . $direction . 'Formatter', $reference);
        }
    }
}
