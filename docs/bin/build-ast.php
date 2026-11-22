#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * Exports the AST node classes of "type-lang/types" (their docblocks, parents,
 * public properties and enum cases) into src/data/toolkit/ast.json, the source
 * of the <AstReference /> component.
 *
 * Usage:
 *   php docs/bin/build-ast.php          writes the file
 *   php docs/bin/build-ast.php --check  fails when the file is outdated
 *
 * Works inside the monorepository only: the classes come from libs/types.
 */

$root = \dirname(__DIR__, 2);
$sources = $root . '/libs/types/src';
$target = \dirname(__DIR__) . '/src/data/toolkit/ast.json';

if (!\is_file($root . '/vendor/autoload.php') || !\is_dir($sources)) {
    \fwrite(\STDERR, 'The script runs inside the TypeLang monorepository only' . \PHP_EOL);

    exit(1);
}

require $root . '/vendor/autoload.php';

const NS = 'TypeLang\\Type\\';

/**
 * Text of a docblock without the tags, `{@see X}` written as code.
 */
function readDocBlock(string|false $comment): string
{
    if ($comment === false) {
        return '';
    }

    $lines = \explode("\n", \str_replace("\r\n", "\n", $comment));
    $result = [];
    $isCode = false;

    foreach ($lines as $line) {
        $line = \preg_replace('/^\s*(?:\/\*\*|\*\/|\*)\s?/', '', $line);

        if (\str_starts_with(\trim($line), '```')) {
            $isCode = !$isCode;
        }

        // Tags end the text: everything from the first one on is for tools.
        if (!$isCode && \str_starts_with(\trim($line), '@')) {
            break;
        }

        $result[] = \rtrim($line);
    }

    $text = \trim(\implode("\n", $result));

    return (string) \preg_replace_callback(
        '/\{@see\s+([^}]+)\}/',
        static fn(array $m): string => '`' . \trim(\str_replace(['\\' . NS, NS], '', $m[1])) . '`',
        $text,
    );
}

function getShortName(string $class): string
{
    return \str_starts_with($class, NS) ? \substr($class, \strlen(NS)) : $class;
}

function printType(?\ReflectionType $type): string
{
    if ($type === null) {
        return '';
    }

    if ($type instanceof \ReflectionNamedType) {
        $name = $type->isBuiltin() ? $type->getName() : getShortName($type->getName());
        $isNullable = $type->allowsNull() && !\in_array($type->getName(), ['mixed', 'null'], true);

        return ($isNullable ? '?' : '') . $name;
    }

    $separator = $type instanceof \ReflectionUnionType ? '|' : '&';

    /** @var \ReflectionUnionType|\ReflectionIntersectionType $type */
    return \implode($separator, \array_map(printType(...), $type->getTypes()));
}

$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sources, \FilesystemIterator::SKIP_DOTS));
$entries = [];

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $relative = \str_replace('\\', '/', \substr($file->getPathname(), \strlen($sources) + 1, -4));
    $class = NS . \str_replace('/', '\\', $relative);

    if (!\class_exists($class) && !\interface_exists($class) && !\enum_exists($class) && !\trait_exists($class)) {
        continue;
    }

    $reflection = new \ReflectionClass($class);
    $kind = match (true) {
        $reflection->isEnum() => 'enum',
        $reflection->isTrait() => 'trait',
        $reflection->isInterface() => 'interface',
        $reflection->isAbstract() => 'abstract',
        default => 'final',
    };

    if (\str_contains(readDocBlock($reflection->getDocComment()), '@internal') || $reflection->isTrait()) {
        continue;
    }

    $properties = [];

    foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
        if ($property->getDeclaringClass()->getName() !== $class || $property->isStatic() || $reflection->isEnum()) {
            continue;
        }

        $properties[] = [
            'name' => $property->getName(),
            'type' => printType($property->getType()),
            'isReadonly' => $property->isReadOnly(),
            'description' => readDocBlock($property->getDocComment()),
        ];
    }

    $cases = [];

    if ($reflection->isEnum()) {
        foreach ((new \ReflectionEnum($class))->getCases() as $case) {
            $cases[] = ['name' => $case->getName(), 'description' => readDocBlock($case->getDocComment())];
        }
    }

    $parent = $reflection->getParentClass();
    $namespace = '';

    if (\str_contains($relative, '/')) {
        $namespace = \explode('/', $relative)[0];
    }

    $entries[] = [
        'id' => getShortName($class),
        'name' => $reflection->getShortName(),
        'group' => $namespace,
        'kind' => $kind,
        'parent' => $parent === false ? null : getShortName($parent->getName()),
        'description' => readDocBlock($reflection->getDocComment()),
        'properties' => $properties,
        'cases' => $cases,
    ];
}

\usort($entries, static fn(array $a, array $b): int => [$a['group'], $a['id']] <=> [$b['group'], $b['id']]);

$json = \json_encode($entries, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR)
    . "\n";

if (\in_array('--check', $argv, true)) {
    if (!\is_file($target) || \file_get_contents($target) !== $json) {
        \fwrite(\STDERR, 'src/data/toolkit/ast.json is outdated, run php docs/bin/build-ast.php' . \PHP_EOL);

        exit(1);
    }

    exit(0);
}

if (!\is_dir(\dirname($target))) {
    \mkdir(\dirname($target), 0o777, true);
}

\file_put_contents($target, $json);

\printf('%d classes written to %s' . \PHP_EOL, \count($entries), $target);
