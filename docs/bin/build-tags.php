#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * Exports the reference data of the phpdoc tags (grammar, examples, aliases)
 * from the model of "type-lang/phpdoc" into src/data/phpdoc/tags.json, the
 * source of the <TagReference /> component.
 *
 * Usage:
 *   php docs/bin/build-tags.php          writes the file
 *   php docs/bin/build-tags.php --check  fails when the file is outdated
 *
 * Works inside the monorepository only: the model and its loader belong to
 * libs/phpdoc.
 */

use TypeLang\PhpDoc\Build\Model\Alias;
use TypeLang\PhpDoc\Build\Model\Example;
use TypeLang\PhpDoc\Build\Model\LinkedModel;
use TypeLang\PhpDoc\Build\Model\ModelException;
use TypeLang\PhpDoc\Build\Model\ModelLinker;
use TypeLang\PhpDoc\Build\Model\ModelLoader;
use TypeLang\PhpDoc\Build\Model\Tag;

$root = \dirname(__DIR__, 2);
$model = $root . '/libs/phpdoc/resources/model';
$target = \dirname(__DIR__) . '/src/data/phpdoc/tags.json';

if (!\is_file($root . '/vendor/autoload.php') || !\is_dir($model)) {
    \fwrite(\STDERR, 'The script runs inside the TypeLang monorepository only' . \PHP_EOL);

    exit(1);
}

require $root . '/vendor/autoload.php';

try {
    $linked = (new ModelLinker())->link((new ModelLoader())->load($model));
} catch (ModelException $e) {
    foreach ($e->errors as $error) {
        \fwrite(\STDERR, $error . \PHP_EOL);
    }

    exit(1);
}

/**
 * Every registered name with the tag it ends up at, the alias it is declared
 * by and whether the alias is a runtime one (in the package of its target).
 *
 * @return array<non-empty-string, array{Tag, Alias|null, bool}>
 */
function getNames(LinkedModel $model): array
{
    $result = [];

    foreach ($model->tags as $tag) {
        if ($tag->package !== null) {
            $result[$tag->name] = [$tag, null, false];
        }
    }

    foreach ($model->model->aliases as $alias) {
        $package = $model->packages[$alias->package];
        $tag = $package->tags[$alias->name] ?? null;
        $next = $alias->target;

        // A runtime alias, possibly through other aliases of the package.
        while ($tag === null && $next !== null) {
            $tag = $package->tags[$next] ?? $model->tags[$next] ?? null;
            $next = $package->aliases[$next] ?? null;
        }

        if ($tag === null) {
            throw new \LogicException(\sprintf('Alias "@%s" does not lead to a tag', $alias->name));
        }

        $result[$alias->name] = [$tag, $alias, isset($package->aliases[$alias->name])];
    }

    \ksort($result, \SORT_STRING | \SORT_FLAG_CASE);

    return $result;
}

/**
 * @param list<Example> $examples
 * @return list<array{title: string|null, code: string}>
 */
function exportExamples(array $examples, ?string $from = null, ?string $to = null): array
{
    $result = [];

    foreach ($examples as $example) {
        $code = $example->code;

        if ($from !== null && $to !== null) {
            $code = (string) \preg_replace('/@' . \preg_quote($from, '/') . '(?![\w:-])/', '@' . $to, $code);
        }

        $result[] = ['title' => $example->title, 'code' => $code];
    }

    return $result;
}

$entries = [];

foreach (getNames($linked) as $name => [$tag, $alias, $isRuntime]) {
    $grammar = [];

    if ($alias === null || $isRuntime) {
        $grammar[] = \sprintf('"@%s" %s', $tag->name, $tag->grammar);
    }

    $aliasInfo = null;
    $examples = exportExamples($tag->examples);
    $canonicalExamples = null;

    if ($alias !== null) {
        $grammar[] = \sprintf('"@%s" %s', $alias->name, $tag->grammar);
        $aliasInfo = ['isRuntime' => $isRuntime];
        $examples = exportExamples($alias->examples);
    }

    if ($alias !== null && $isRuntime) {
        $canonicalExamples = exportExamples($alias->examples, $alias->name, $tag->name);
    }

    $entries[] = [
        'id' => $name,
        'tag' => $tag->name,
        'class' => $tag->class,
        'alias' => $aliasInfo,
        'summary' => $tag->summary,
        'since' => $tag->since,
        'deprecated' => $tag->deprecated,
        'registrations' => $linked->getRegistrations($tag),
        'grammar' => $grammar,
        'examples' => $examples,
        'canonicalExamples' => $canonicalExamples,
    ];
}

$json = \json_encode($entries, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR)
    . "\n";

if (\in_array('--check', $argv, true)) {
    if (!\is_file($target) || \file_get_contents($target) !== $json) {
        \fwrite(\STDERR, 'src/data/phpdoc/tags.json is outdated, run php docs/bin/build-tags.php' . \PHP_EOL);

        exit(1);
    }

    exit(0);
}

if (!\is_dir(\dirname($target))) {
    \mkdir(\dirname($target), 0o777, true);
}

\file_put_contents($target, $json);

\printf('%d tags written to %s' . \PHP_EOL, \count($entries), $target);
