<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Passes the extensions to the mappers. The extensions of a kind of a
 * named mapper are its own ones, then the ones of the default mapper. The
 * extensions of a mapper are the ones of its config in the order of the
 * config, then the tagged ones by the priority. A service tagged without
 * the `mapper` attribute extends the default mapper. A coercer of a type
 * or a formatter of a direction of a named mapper replaces the one of the
 * default mapper, see {@see MapperSlot}. The type
 * recorder of the profiler is the last decorator, it sees the final type.
 * The function providers tagged for the expression language of the bundle
 * are passed to it as a list, by the priority.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Symfony
 */
final class MapperExtensionsPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * @throws InvalidArgumentException in case of a service is tagged for a
     *         mapper that is not configured
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(MapperDefinitions::EXPRESSION_LANGUAGE_ID)) {
            $container->getDefinition(MapperDefinitions::EXPRESSION_LANGUAGE_ID)->setArgument(
                1,
                $this->findAndSortTaggedServices(MapperDefinitions::EXPRESSION_LANGUAGE_PROVIDER_TAG, $container),
            );
        }

        if (!$container->hasParameter(MapperDefinitions::EXTENSIONS_PARAMETER)) {
            return;
        }

        /**
         * @var array<
         *     non-empty-string,
         *     array<value-of<ExtensionKind>, list<non-empty-string>>,
         * >
         */
        $services = $container->getParameter(MapperDefinitions::EXTENSIONS_PARAMETER);
        $container->getParameterBag()->remove(MapperDefinitions::EXTENSIONS_PARAMETER);

        foreach (ExtensionKind::cases() as $kind) {
            foreach ($this->getTaggedServices($container, $kind, \array_keys($services)) as $name => $ids) {
                $services[$name][$kind->value] = [...$services[$name][$kind->value], ...$ids];
            }
        }

        $defaults = $services[MapperDefinitions::DEFAULT_NAME];

        foreach ($services as $name => $own) {
            foreach (ExtensionKind::cases() as $kind) {
                $ids = $own[$kind->value];

                if ($name !== MapperDefinitions::DEFAULT_NAME) {
                    $ids = [...$ids, ...$defaults[$kind->value]];
                }

                $references = [];

                foreach (\array_unique($ids) as $id) {
                    $references[] = new Reference($id);
                }

                $recorder = MapperDefinitions::getTypeRecorderId($name);

                if ($kind === ExtensionKind::TypeDecorator && $container->hasDefinition($recorder)) {
                    $references[] = new Reference($recorder);
                }

                $kind->addServices(
                    $container->getDefinition(MapperDefinitions::getExtensionTargetId($name, $kind)),
                    $references,
                );
            }
        }

        /**
         * @var array<
         *     non-empty-string,
         *     array<
         *         value-of<MapperSlot>,
         *         array<non-empty-string, non-empty-string>,
         *     >,
         * >
         */
        $slots = $container->getParameter(MapperDefinitions::SLOTS_PARAMETER);
        $container->getParameterBag()->remove(MapperDefinitions::SLOTS_PARAMETER);

        foreach (MapperSlot::cases() as $slot) {
            $configured = [];

            foreach ($slots as $name => $services) {
                $configured[$name] = $services[$slot->value];
            }

            $this->processSlot($container, $slot, $configured);
        }
    }

    /**
     * Passes the services of the role to the mappers: the own service of a
     * key, then the one of the default mapper, then the default of the core.
     *
     * @param array<
     *     non-empty-string,
     *     array<non-empty-string, non-empty-string>,
     * > $services configured services of the mappers by the key
     * @throws InvalidArgumentException in case of a mapper is given two
     *         services of one key or a service of an unknown key
     */
    private function processSlot(ContainerBuilder $container, MapperSlot $slot, array $services): void
    {
        $mappers = \array_keys($services);

        /** @var array<non-empty-string, list<array<array-key, mixed>>> */
        $tagged = $container->findTaggedServiceIds($slot->value);

        foreach ($tagged as $id => $tags) {
            foreach ($tags as $attributes) {
                $mapper = self::getTaggedMapper($id, $slot->value, $attributes, $mappers);
                $key = $attributes[$slot->getKeyName()] ?? null;

                if (!\is_string($key) || !\in_array($key, $slot->getKeys(), true)) {
                    throw new InvalidArgumentException(\sprintf(
                        'Service "%s" is tagged "%s" with %s "%s", supported ones are %s',
                        \addcslashes($id, '"'),
                        $slot->value,
                        $slot->getKeyName(),
                        \addcslashes(\is_string($key) ? $key : \get_debug_type($key), '"'),
                        \implode(', ', $slot->getKeys()),
                    ));
                }

                if (isset($services[$mapper][$key])) {
                    throw new InvalidArgumentException(\sprintf(
                        'Mapper "%s" is given two %ss of %s "%s": "%s" and "%s"',
                        \addcslashes($mapper, '"'),
                        $slot->getName(),
                        $slot->getKeyName(),
                        \addcslashes($key, '"'),
                        \addcslashes($services[$mapper][$key], '"'),
                        \addcslashes($id, '"'),
                    ));
                }

                $services[$mapper][$key] = $id;
            }
        }

        $defaults = $services[MapperDefinitions::DEFAULT_NAME] ?? [];

        foreach ($mappers as $name) {
            $references = [];

            foreach ([...$defaults, ...$services[$name]] as $key => $id) {
                $references[$key] = new Reference($id);
            }

            $slot->setServices($container->getDefinition(MapperDefinitions::getMapperId($name)), $references);
        }
    }

    /**
     * Returns the mapper of the tag, the default one in case of the tag
     * names none.
     *
     * @param array<array-key, mixed> $attributes
     * @param list<non-empty-string> $mappers
     * @return non-empty-string
     * @throws InvalidArgumentException in case of the mapper is not
     *         configured
     */
    private static function getTaggedMapper(string $id, string $tag, array $attributes, array $mappers): string
    {
        $mapper = $attributes['mapper'] ?? MapperDefinitions::DEFAULT_NAME;

        if (\is_string($mapper) && \in_array($mapper, $mappers, true)) {
            return $mapper;
        }

        throw new InvalidArgumentException(\sprintf(
            'Service "%s" is tagged "%s" for mapper "%s" that is not configured',
            \addcslashes($id, '"'),
            $tag,
            \addcslashes(\is_string($mapper) ? $mapper : \get_debug_type($mapper), '"'),
        ));
    }

    /**
     * Returns the ids of the services tagged with the kind by the name of
     * the mapper, sorted by the priority.
     *
     * @param list<non-empty-string> $mappers
     * @return array<non-empty-string, list<non-empty-string>>
     * @throws InvalidArgumentException in case of a service is tagged for a
     *         mapper that is not configured
     */
    private function getTaggedServices(ContainerBuilder $container, ExtensionKind $kind, array $mappers): array
    {
        $found = [];

        /** @var array<non-empty-string, list<array<array-key, mixed>>> */
        $tagged = $container->findTaggedServiceIds($kind->value);

        foreach ($tagged as $id => $tags) {
            foreach ($tags as $attributes) {
                $mapper = self::getTaggedMapper($id, $kind->value, $attributes, $mappers);
                $priority = $attributes['priority'] ?? 0;

                $found[$mapper][] = ['id' => $id, 'priority' => \is_int($priority) ? $priority : 0];
            }
        }

        $result = [];

        foreach ($found as $mapper => $entries) {
            \usort($entries, static fn(array $a, array $b): int => $b['priority'] <=> $a['priority']);

            foreach ($entries as $entry) {
                $result[$mapper][] = $entry['id'];
            }
        }

        return $result;
    }
}
