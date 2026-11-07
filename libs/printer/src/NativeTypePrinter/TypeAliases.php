<?php

declare(strict_types=1);

namespace TypeLang\Printer\NativeTypePrinter;

/**
 * The map of the names of virtual types to the native types they stand for.
 *
 * The builtin names are the ones of Phan, Psalm and PHPStan, taken in this
 * order of priority from `resources/aliases`. The names added by hand win.
 */
final class TypeAliases
{
    /**
     * @var array<non-empty-lowercase-string, non-empty-string>|null
     */
    private static ?array $builtin = null;

    /**
     * @var array<non-empty-lowercase-string, non-empty-string>
     */
    private array $aliases;

    public function __construct()
    {
        $this->aliases = self::getBuiltin();
    }

    /**
     * @return array<non-empty-lowercase-string, non-empty-string>
     */
    private static function getBuiltin(): array
    {
        if (self::$builtin !== null) {
            return self::$builtin;
        }

        $aliases = [];

        foreach (['phan', 'psalm', 'phpstan'] as $name) {
            /** @var array<non-empty-lowercase-string, non-empty-string> */
            $table = require __DIR__ . '/../../resources/aliases/' . $name . '.php';

            $aliases += $table;
        }

        return self::$builtin = $aliases;
    }

    /**
     * @param non-empty-string $alias
     * @param non-empty-string $type
     */
    public function add(string $alias, string $type): void
    {
        $this->aliases[\strtolower($alias)] = $type;
    }

    /**
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addUnion(string $alias, array $types): void
    {
        \sort($types);

        $this->add($alias, \implode('|', $types));
    }

    /**
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addIntersection(string $alias, array $types): void
    {
        \sort($types);

        $this->add($alias, \implode('&', $types));
    }

    /**
     * Gets the type a name stands for, or the name itself in case of it has
     * no alias.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public function resolve(string $name): string
    {
        return $this->aliases[\strtolower($name)] ?? $name;
    }
}
