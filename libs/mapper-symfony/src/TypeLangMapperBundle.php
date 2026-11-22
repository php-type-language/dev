<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperCoercer;
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperFormatter;
use TypeLang\Mapper\Bridge\Symfony\Attribute\MapperExtensionAttribute;
use TypeLang\Mapper\Bridge\Symfony\DependencyInjection\ConfigDefinition;
use TypeLang\Mapper\Bridge\Symfony\DependencyInjection\ExtensionKind;
use TypeLang\Mapper\Bridge\Symfony\DependencyInjection\MapperDefinitions;
use TypeLang\Mapper\Bridge\Symfony\DependencyInjection\MapperExtensionsPass;
use TypeLang\Mapper\Bridge\Symfony\DependencyInjection\MapperSlot;

/**
 * Registers the default mapper (`type_lang_mapper.mapper`) and the named
 * mappers extending it (`type_lang_mapper.mapper.<name>`) from the
 * `type_lang_mapper` config.
 */
final class TypeLangMapperBundle extends AbstractBundle
{
    protected string $extensionAlias = 'type_lang_mapper';

    public function configure(DefinitionConfigurator $definition): void
    {
        $root = $definition->rootNode();

        \assert($root instanceof ArrayNodeDefinition);

        ConfigDefinition::define($root);
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->hasExtension('framework')) {
            return;
        }

        $container->extension('framework', [
            'cache' => [
                'pools' => [
                    MapperDefinitions::DEFAULT_CACHE_POOL => ['adapter' => 'cache.system'],
                ],
            ],
        ]);
    }

    /**
     * @param array<array-key, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        (new MapperDefinitions($builder))->register($config);
    }

    public function build(ContainerBuilder $container): void
    {
        foreach (ExtensionKind::cases() as $kind) {
            self::registerExtensionAttribute($container, $kind);
        }

        foreach (MapperSlot::cases() as $slot) {
            self::registerSlotAttribute($container, $slot);
        }

        $container->addCompilerPass(new MapperExtensionsPass());
    }

    private static function registerSlotAttribute(ContainerBuilder $container, MapperSlot $slot): void
    {
        $container->registerAttributeForAutoconfiguration(
            $slot->getAttribute(),
            static function (
                ChildDefinition $definition,
                AsMapperCoercer|AsMapperFormatter $attribute,
                \Reflector $class,
            ) use ($slot): void {
                \assert($class instanceof \ReflectionClass);

                self::assertImplements($class, $slot->getInterface(), $slot->getAttribute());

                $key = $slot->getAttributeKey($attribute);

                if (!\in_array($key, $slot->getKeys(), true)) {
                    throw new \LogicException(\sprintf(
                        'Class "%s" is declared as the %s of %s "%s", supported ones are %s',
                        \addcslashes($class->name, '"'),
                        $slot->getName(),
                        $slot->getKeyName(),
                        \addcslashes($key, '"'),
                        \implode(', ', $slot->getKeys()),
                    ));
                }

                foreach (self::getTaggedMappers($attribute->mappers) as $mapper) {
                    $definition->addTag($slot->value, \array_filter(
                        [$slot->getKeyName() => $key, 'mapper' => $mapper],
                        static fn(mixed $value): bool => $value !== null,
                    ));
                }
            },
        );
    }

    private static function registerExtensionAttribute(ContainerBuilder $container, ExtensionKind $kind): void
    {
        $container->registerAttributeForAutoconfiguration(
            $kind->getAttribute(),
            static function (
                ChildDefinition $definition,
                MapperExtensionAttribute $attribute,
                \Reflector $class,
            ) use ($kind): void {
                \assert($class instanceof \ReflectionClass);

                self::assertImplements($class, $kind->getInterface(), $kind->getAttribute());

                foreach (self::getTaggedMappers($attribute->mappers) as $mapper) {
                    $definition->addTag($kind->value, \array_filter(
                        ['mapper' => $mapper, 'priority' => $attribute->priority],
                        static fn(mixed $value): bool => $value !== null,
                    ));
                }
            },
        );
    }

    /**
     * @param \ReflectionClass<object> $class
     * @param class-string $interface
     * @param class-string $attribute
     * @throws \LogicException in case of the class does not implement the
     *         interface
     */
    private static function assertImplements(\ReflectionClass $class, string $interface, string $attribute): void
    {
        if ($class->implementsInterface($interface)) {
            return;
        }

        throw new \LogicException(\sprintf(
            'Class "%s" is marked with #[%s], but does not implement "%s"',
            \addcslashes($class->name, '"'),
            $attribute,
            $interface,
        ));
    }

    /**
     * Returns the mappers of the tags, {@see null} is the default mapper.
     *
     * @param list<non-empty-string> $mappers
     * @return non-empty-list<non-empty-string|null>
     */
    private static function getTaggedMappers(array $mappers): array
    {
        if ($mappers === []) {
            return [null];
        }

        return $mappers;
    }
}
