<?php

declare(strict_types=1);

namespace TypeLang\Parser\TypeResolver;

final readonly class PhpUseStatementsReader
{
    /**
     * Return array of use statements from class.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return array<int|non-empty-string, non-empty-string>
     */
    public function getUseStatements(\ReflectionClass $class): array
    {
        try {
            $header = $this->getCodeHeader($class);
        } catch (\Throwable) {
            $header = '';
        }

        return [...$this->parse($class, $header)];
    }

    /**
     * Read file source up to the line where our class is defined.
     *
     * @param \ReflectionClass<object> $class
     *
     * @throws \LogicException
     * @throws \RuntimeException
     */
    private function getCodeHeader(\ReflectionClass $class): string
    {
        $pathname = $class->getFileName();

        if ($pathname === false || !$class->isUserDefined()) {
            return '';
        }

        $source = new \SplFileObject($pathname);
        $source->flock(\LOCK_SH);

        $line = 0;
        $result = '';

        while (!$source->eof()) {
            if (++$line >= $class->getStartLine()) {
                break;
            }

            $result .= $source->fgets();
        }

        $source->flock(\LOCK_UN);
        unset($source);

        return $result;
    }

    /**
     * @return \Iterator<array-key, \PhpToken>
     */
    private function lex(string $source): \Iterator
    {
        yield from \PhpToken::tokenize($source);
    }

    /**
     * @param \Iterator<array-key, \PhpToken> $tokens
     */
    private function readNamespace(\Iterator $tokens): string
    {
        // Skip "namespace" token.
        $tokens->next();

        $result = null;

        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id === \T_NAME_QUALIFIED || $current->id === \T_STRING) {
                $result = $current->text;
            } elseif ($current->text === ';' || $current->text === '{') {
                // A namespace name is terminated either by ";" or "{"
                $tokens->next();

                return $result ?? '';
            }

            $tokens->next();
        }

        return $result ?? '';
    }

    /**
     * @param \ReflectionClass<object> $class
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return \Iterator<array-key, \PhpToken>
     */
    private function skipUnimportantNamespaces(\ReflectionClass $class, \Iterator $tokens): \Iterator
    {
        $expected = $class->getNamespaceName();

        $atLeastOneNamespace = false;

        while ($tokens->valid()) {
            $current = $tokens->current();

            switch ($current->id) {
                case \T_NAMESPACE:
                    $atLeastOneNamespace = true;
                    if ($this->readNamespace($tokens) === $expected) {
                        return $tokens;
                    }
                    break;

                case \T_USE:
                    if ($atLeastOneNamespace === false) {
                        return $tokens;
                    }
                    break;
            }

            $tokens->next();
        }

        return $tokens;
    }

    /**
     * Reads the type imports of the current namespace.
     *
     * Import statements always precede any other statement in a namespace, so
     * reading stops as soon as a token that cannot open a "use" statement is
     * reached — function/class declarations, closures and so on are never
     * descended into.
     *
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return \Iterator<int|non-empty-string, non-empty-string>
     */
    private function readImports(\Iterator $tokens): \Iterator
    {
        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id === \T_WHITESPACE
                || $current->id === \T_COMMENT
                || $current->id === \T_DOC_COMMENT
            ) {
                $tokens->next();

                continue;
            }

            // The first token that does not open a "use" statement ends the
            // import section of the namespace.
            if ($current->id !== \T_USE) {
                break;
            }

            // Skip the "use" keyword.
            $tokens->next();

            foreach ($this->fetchUseStatement($tokens) as [$namespace, $alias]) {
                if ($alias === null) {
                    yield $namespace;
                } else {
                    yield $alias => $namespace;
                }
            }
        }
    }

    /**
     * Reads a single "use" statement (the tokens after the "use" keyword up to
     * and including the terminating ";") and returns the imports it declares.
     *
     * A statement may declare several imports — a comma-separated list or a
     * group "use" — and covers "use function"/"use const" alike.
     *
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return list<array{non-empty-string, non-empty-string|null}>
     */
    private function fetchUseStatement(\Iterator $tokens): array
    {
        $buffer = $this->readStatementTokens($tokens);

        $offset = 0;

        // "use function Some\fn;" and "use const Some\CONST;" are imports too;
        // the "function"/"const" modifier is not part of the imported name.
        if (isset($buffer[0]) && ($buffer[0]->id === \T_FUNCTION || $buffer[0]->id === \T_CONST)) {
            $offset = 1;
        }

        $entries = \array_slice($buffer, $offset);
        $prefix = null;

        // A group "use Some\Prefix\{ A, B as C };" shares a common prefix; peel
        // it off and read the brace-enclosed entries against it.
        $open = $this->offsetOf($entries, '{');

        if ($open !== null) {
            $prefix = $this->readName(\array_slice($entries, 0, $open));

            if ($prefix === '') {
                return [];
            }

            $close = $this->offsetOf($entries, '}') ?? \count($entries);
            $entries = \array_slice($entries, $open + 1, $close - $open - 1);
        }

        return $this->parseEntries($entries, $prefix);
    }

    /**
     * Consumes the tokens of the current statement up to and including the
     * terminating ";", returning the significant (non-whitespace, non-comment)
     * ones.
     *
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return list<\PhpToken>
     */
    private function readStatementTokens(\Iterator $tokens): array
    {
        $buffer = [];

        while ($tokens->valid()) {
            $current = $tokens->current();
            $tokens->next();

            if ($current->text === ';') {
                break;
            }

            if ($current->id === \T_WHITESPACE
                || $current->id === \T_COMMENT
                || $current->id === \T_DOC_COMMENT
            ) {
                continue;
            }

            $buffer[] = $current;
        }

        return $buffer;
    }

    /**
     * Splits a comma-separated list of import entries and parses each of them,
     * optionally prepending a shared group prefix.
     *
     * @param list<\PhpToken> $tokens
     * @param non-empty-string|null $prefix
     *
     * @return list<array{non-empty-string, non-empty-string|null}>
     */
    private function parseEntries(array $tokens, ?string $prefix): array
    {
        $imports = [];
        $entry = [];

        foreach ($tokens as $token) {
            if ($token->text === ',') {
                $import = $this->parseEntry($entry, $prefix);

                if ($import !== null) {
                    $imports[] = $import;
                }

                $entry = [];

                continue;
            }

            $entry[] = $token;
        }

        $import = $this->parseEntry($entry, $prefix);

        if ($import !== null) {
            $imports[] = $import;
        }

        return $imports;
    }

    /**
     * @param list<\PhpToken> $tokens
     *
     * @return int<0, max>|null
     */
    private function offsetOf(array $tokens, string $text): ?int
    {
        foreach ($tokens as $offset => $token) {
            if ($token->text === $text) {
                return $offset;
            }
        }

        return null;
    }

    /**
     * Parses a single "Name (as Alias)?" import entry, prepending the group
     * prefix when one is given.
     *
     * @param list<\PhpToken> $tokens
     * @param non-empty-string|null $prefix
     *
     * @return array{non-empty-string, non-empty-string|null}|null
     */
    private function parseEntry(array $tokens, ?string $prefix): ?array
    {
        $nameTokens = [];
        $alias = null;
        $afterAs = false;

        foreach ($tokens as $token) {
            if ($token->id === \T_AS) {
                $afterAs = true;

                continue;
            }

            if ($afterAs) {
                if ($token->id === \T_STRING) {
                    $alias = $token->text;
                }

                continue;
            }

            $nameTokens[] = $token;
        }

        $name = $this->readName($nameTokens);

        if ($name === '') {
            return null;
        }

        if ($prefix !== null) {
            $name = $prefix . '\\' . $name;
        }

        return [$name, $alias === '' ? null : $alias];
    }

    /**
     * Joins a sequence of name tokens into a namespace string, trimming any
     * leading (fully qualified) or trailing (group prefix) separators.
     *
     * @param list<\PhpToken> $tokens
     */
    private function readName(array $tokens): string
    {
        $result = '';

        foreach ($tokens as $token) {
            if ($token->id === \T_STRING
                || $token->id === \T_NAME_QUALIFIED
                || $token->id === \T_NAME_FULLY_QUALIFIED
                || $token->id === \T_NS_SEPARATOR
            ) {
                $result .= $token->text;
            }
        }

        return \trim($result, '\\');
    }

    /**
     * Parse the use statements from read source by tokenizing and reading the
     * tokens. Returns an array of use statements and aliases.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return \Iterator<int|non-empty-string, non-empty-string>
     */
    private function parse(\ReflectionClass $class, string $source): \Iterator
    {
        $tokens = $this->lex($source);

        $tokens = $this->skipUnimportantNamespaces($class, $tokens);

        return $this->readImports($tokens);
    }
}
