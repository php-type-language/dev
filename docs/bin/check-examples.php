#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * Checks the type examples of the documentation against the PHP parser: every
 * <TypeTree type="..." /> must parse, every <TypeTree type="..." error /> must
 * be rejected, with the error of its `message` when one is given.
 *
 * Usage:
 *   php docs/bin/check-examples.php
 *
 * Works inside the monorepository only: the parser comes from libs/parser.
 */

use TypeLang\Parser\TypeParser;

$root = \dirname(__DIR__, 2);
$pages = \dirname(__DIR__) . '/src/content/docs';

if (!\is_file($root . '/vendor/autoload.php')) {
    \fwrite(\STDERR, 'The script runs inside the TypeLang monorepository only' . \PHP_EOL);

    exit(1);
}

require $root . '/vendor/autoload.php';

/**
 * Value of a JSX attribute: `name="text"` or `name={"json string"}`.
 */
function readAttribute(string $tag, string $name): ?string
{
    if (\preg_match('/\s' . $name . '=\{("(?:[^"\\\\]|\\\\.)*")\}/', $tag, $matches) === 1) {
        $value = \json_decode($matches[1], flags: \JSON_THROW_ON_ERROR);

        \assert(\is_string($value));

        return $value;
    }

    if (\preg_match('/\s' . $name . '="([^"]*)"/', $tag, $matches) === 1) {
        return $matches[1];
    }

    return null;
}

$parser = new TypeParser();
$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pages, \FilesystemIterator::SKIP_DOTS));
$checked = 0;
$failures = [];

foreach ($files as $file) {
    if ($file->getExtension() !== 'mdx') {
        continue;
    }

    $text = (string) \file_get_contents($file->getPathname());
    $name = \substr($file->getPathname(), \strlen($pages) + 1);

    \preg_match_all('/<TypeTree\b(?:[^>"{]|"[^"]*"|\{"(?:[^"\\\\]|\\\\.)*"\})*>/', $text, $matches);

    foreach ($matches[0] as $tag) {
        $type = readAttribute($tag, 'type');

        if ($type === null) {
            $failures[] = \sprintf('%s: <TypeTree> without a type: %s', $name, $tag);

            continue;
        }

        $isError = \preg_match('/\serror[\s\/>]/', $tag) === 1;
        $expected = readAttribute($tag, 'message');
        $actual = null;

        try {
            $parser->parse($type);
        } catch (\Throwable $e) {
            $actual = (new \ReflectionClass($e))->getShortName() . ': ' . $e->getMessage();
        }

        ++$checked;

        if (!$isError && $actual !== null) {
            $failures[] = \sprintf('%s: "%s" must parse, but %s', $name, $type, $actual);
        } elseif ($isError && $actual === null) {
            $failures[] = \sprintf('%s: "%s" must be rejected, but parses', $name, $type);
        } elseif ($expected !== null && $expected !== $actual) {
            $failures[] = \sprintf('%s: "%s" is documented as "%s", the parser says "%s"', $name, $type, $expected, $actual);
        }
    }
}

foreach ($failures as $failure) {
    \fwrite(\STDERR, $failure . \PHP_EOL);
}

\printf('%d examples checked, %d failed' . \PHP_EOL, $checked, \count($failures));

exit($failures === [] ? 0 : 1);
