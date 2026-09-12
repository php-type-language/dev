<?php

declare(strict_types=1);

namespace TypeLang\Bench\PhpDoc\Tools;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

abstract class DocBlockParserBench
{
    /**
     * A sandbox directory containing the third-party packages that are used
     * as a real-world DocBlock corpus.
     *
     * @var non-empty-string
     */
    protected const CORPUS_DIRECTORY = __DIR__ . '/../../var/corpus';

    /**
     * The packages to extract the DocBlocks from.
     *
     * @var non-empty-array<non-empty-string, non-empty-string>
     */
    protected const CORPUS_PACKAGES = [
        'doctrine/collections' => '*',
        'doctrine/lexer' => '*',
        'guzzlehttp/guzzle' => '*',
        'guzzlehttp/promises' => '*',
        'guzzlehttp/psr7' => '*',
        'illuminate/support' => '*',
        'monolog/monolog' => '*',
        'nikic/php-parser' => '*',
        'phpdocumentor/reflection-docblock' => '*',
        'phpstan/extension-installer' => '*',
        'phpstan/php-8-stubs' => '*',
        'phpstan/phpdoc-parser' => '*',
        'phpstan/phpstan' => '*',
        'phpstan/phpstan-beberlei-assert' => '*',
        'phpstan/phpstan-deprecation-rules' => '*',
        'phpstan/phpstan-dibi' => '*',
        'phpstan/phpstan-doctrine' => '*',
        'phpstan/phpstan-mockery' => '*',
        'phpstan/phpstan-nette' => '*',
        'phpstan/phpstan-php-parser' => '*',
        'phpstan/phpstan-phpunit' => '*',
        'phpstan/phpstan-strict-rules' => '*',
        'phpstan/phpstan-symfony' => '*',
        'phpstan/phpstan-webmozart-assert' => '*',
        'psalm/attributes' => '*',
        'psalm/plugin-mockery' => '*',
        'psalm/plugin-phpunit' => '*',
        'psalm/plugin-symfony' => '*',
        'phpunit/php-code-coverage' => '*',
        'phpunit/php-file-iterator' => '*',
        'phpunit/php-text-template' => '*',
        'phpunit/php-timer' => '*',
        'phpunit/phpunit' => '*',
        'psr/container' => '*',
        'psr/http-message' => '*',
        'psr/log' => '*',
        'ralouphie/getallheaders' => '*',
        'sebastian/comparator' => '*',
        'sebastian/diff' => '*',
        'sebastian/environment' => '*',
        'sebastian/exporter' => '*',
        'sebastian/recursion-context' => '*',
        'symfony/console' => '*',
        'symfony/event-dispatcher' => '*',
        'symfony/finder' => '*',
        'symfony/http-foundation' => '*',
        'symfony/polyfill-ctype' => '*',
        'symfony/polyfill-mbstring' => '*',
        'symfony/process' => '*',
        'symfony/service-contracts' => '*',
        'symfony/var-dumper' => '*',
        'twig/twig' => '*',
        'vimeo/psalm' => '*',
        'webmozart/assert' => '*',
    ];

    /**
     * The PHP version the corpus is resolved for.
     *
     * The corpus is read and never run, so the version it is installed for is
     * not the one the benchmark runs on: it is pinned so that the corpus is
     * the same everywhere, and so that a package supporting no version as new
     * as the one at hand is installed all the same.
     *
     * @var non-empty-string
     */
    protected const CORPUS_PHP_VERSION = '8.3.0';

    /**
     * Each set contains every DocBlock of a single package of the corpus.
     *
     * The corpus is installed by the provider itself, because it is executed
     * in a separate process before any benchmark is launched.
     *
     * @return iterable<non-empty-string, array{docblocks: list<non-empty-string>}>
     * @throws \JsonException
     */
    public static function docBlocksDataProvider(): iterable
    {
        $metadata = self::getCorpusMetadata();

        foreach (\array_keys(self::CORPUS_PACKAGES) as $package) {
            $directory = \realpath($metadata['versions'][$package]['install_path'] ?? '');

            if ($directory === false) {
                throw new \RuntimeException(\sprintf(
                    'The "%s" package of the benchmark corpus is not installed',
                    $package,
                ));
            }

            $docblocks = self::extractDocBlocks($directory);

            if ($docblocks === []) {
                continue;
            }

            yield $package => ['docblocks' => $docblocks];
        }
    }

    /**
     * Installs the corpus (if required) and returns the Composer metadata
     * of the installed packages.
     *
     * @return array{versions: array<non-empty-string, array{install_path?: string}>}
     * @throws \JsonException
     */
    private static function getCorpusMetadata(): array
    {
        $installed = self::CORPUS_DIRECTORY . '/vendor/composer/installed.php';

        // The manifest is generated from the {@see CORPUS_PACKAGES} list, so
        // that an edit of the list is enough to update the corpus.
        $outdated = self::updateCorpusManifest();

        if ($outdated || !\is_file(self::CORPUS_DIRECTORY . '/composer.lock')) {
            self::composer('update');
        } elseif (!\is_file($installed)) {
            self::composer('install');
        }

        /** @var array{versions: array<non-empty-string, array{install_path?: string}>} */
        return require $installed;
    }

    /**
     * Writes the "composer.json" of the corpus and returns {@see true} in
     * case of the manifest has been changed (and therefore the installed
     * packages are outdated).
     */
    private static function updateCorpusManifest(): bool
    {
        $pathname = self::CORPUS_DIRECTORY . '/composer.json';

        $expected = \json_encode([
            'name' => 'type-lang/bench-corpus',
            'description' => 'A disposable sandbox of the third-party packages that are '
                . 'used as a real-world DocBlock corpus. The directory is generated by '
                . 'the benchmarks and can be safely removed.',
            'type' => 'project',
            'license' => 'MIT',
            'require' => self::CORPUS_PACKAGES,
            'config' => [
                'preferred-install' => ['*' => 'dist'],
                'allow-plugins' => false,
                'platform' => ['php' => self::CORPUS_PHP_VERSION],
            ],
            'minimum-stability' => 'stable',
            'prefer-stable' => true,
        ], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR);

        if (@\file_get_contents($pathname) === $expected) {
            return false;
        }

        if (!\is_dir(self::CORPUS_DIRECTORY) && !@\mkdir(self::CORPUS_DIRECTORY, recursive: true)) {
            throw new \RuntimeException(\sprintf(
                'Could not create the benchmark corpus directory "%s"',
                self::CORPUS_DIRECTORY,
            ));
        }

        if (\file_put_contents($pathname, $expected) === false) {
            throw new \RuntimeException(\sprintf(
                'Could not write the benchmark corpus manifest "%s"',
                $pathname,
            ));
        }

        return true;
    }

    /**
     * @param non-empty-string $command
     */
    private static function composer(string $command): void
    {
        $process = new Process(
            command: [
                ...self::getComposerBinary(),
                $command,
                '--working-dir=' . self::CORPUS_DIRECTORY,
                '--no-interaction',
                '--no-progress',
                '--no-audit',
                '--no-plugins',
                '--no-scripts',
                '--prefer-dist',
            ],
            // The installation of the corpus may take a few minutes on the
            // first run, so it should not be interrupted by a timeout.
            timeout: null,
        );

        if ($process->run() !== 0) {
            throw new \RuntimeException(\sprintf(
                "Could not install the benchmark corpus:\n%s",
                \trim($process->getErrorOutput() . "\n" . $process->getOutput()),
            ));
        }
    }

    /**
     * @return non-empty-list<non-empty-string>
     */
    private static function getComposerBinary(): array
    {
        $finder = new ExecutableFinder();

        foreach (['composer', 'composer.phar'] as $name) {
            $pathname = $finder->find($name);

            if ($pathname === null || $pathname === '') {
                continue;
            }

            // A phar is not executable on all platforms, so it is passed
            // to the PHP binary that runs the benchmarks.
            return \str_ends_with($pathname, '.phar')
                ? [\PHP_BINARY, $pathname]
                : [$pathname];
        }

        throw new \RuntimeException(\sprintf(
            'The "composer" executable is required to install the benchmark corpus, '
                . 'but it could not be found. Please install the corpus manually '
                . 'using the "composer install --working-dir=%s" command',
            self::CORPUS_DIRECTORY,
        ));
    }

    /**
     * Extracts every DocBlock of every PHP file of the given directory.
     *
     * @param non-empty-string $directory
     * @return list<non-empty-string>
     */
    private static function extractDocBlocks(string $directory): array
    {
        $result = [];

        foreach (self::getSourceFiles($directory) as $pathname) {
            $source = @\file_get_contents($pathname);

            if ($source === false || $source === '') {
                continue;
            }

            foreach (\PhpToken::tokenize($source) as $token) {
                if ($token->is(\T_DOC_COMMENT) && $token->text !== '') {
                    $result[] = $token->text;
                }
            }
        }

        return $result;
    }

    /**
     * @param non-empty-string $directory
     * @return iterable<array-key, non-empty-string>
     */
    private static function getSourceFiles(string $directory): iterable
    {
        $files = new \RecursiveIteratorIterator(
            iterator: new \RecursiveDirectoryIterator(
                directory: $directory,
                flags: \FilesystemIterator::SKIP_DOTS
                    | \FilesystemIterator::CURRENT_AS_FILEINFO,
            ),
        );

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $pathname = $file->getPathname();

            if ($file->isFile() && $file->getExtension() === 'php' && $pathname !== '') {
                yield $pathname;
            }
        }
    }

    /**
     * @param array{docblocks: list<non-empty-string>} $params
     */
    abstract public function benchParseDocBlock(array $params): void;
}
