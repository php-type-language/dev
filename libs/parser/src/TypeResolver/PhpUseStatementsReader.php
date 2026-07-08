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
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return \Iterator<int|non-empty-string, non-empty-string>
     */
    private function lookupUseStatements(\Iterator $tokens): \Iterator
    {
        while ($tokens->valid()) {
            $current = $tokens->current();

            if ($current->id === \T_USE) {
                $tokens->next();

                foreach ($this->fetchUseStatement($tokens) as [$namespace, $alias]) {
                    if ($alias === null) {
                        yield $namespace;
                    } else {
                        yield $alias => $namespace;
                    }
                }

                continue;
            }

            $tokens->next();
        }

        return $tokens;
    }

    /**
     * @param \Iterator<array-key, \PhpToken> $tokens
     *
     * @return list<array{non-empty-string, non-empty-string|null}>
     */
    private function fetchUseStatement(\Iterator $tokens): array
    {
        $buffer = $this->readStatementTokens($tokens);

        if ($buffer === []) {
            return [];
        }

        // A closure "use (...)" capture clause is not an import statement.
        if ($buffer[0]->text === '(') {
            return [];
        }

        // "use function Some\fun;" and "use const Some\CONST;" are imports too;
        // drop the leading "function"/"const" keyword and read the rest as usual.
        if ($buffer[0]->id === \T_FUNCTION || $buffer[0]->id === \T_CONST) {
            \array_shift($buffer);
        }

        if ($buffer === []) {
            return [];
        }

        $braceOffset = $this->indexOfBrace($buffer);

        // Plain "use Some\Name (as Alias)?;".
        if ($braceOffset === null) {
            $import = $this->parseImport($buffer);

            return $import === null ? [] : [$import];
        }

        // Group "use Some\Prefix\{ Name (as Alias)?, ... };".
        return $this->parseGroupImports($buffer, $braceOffset);
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
     * @param list<\PhpToken> $buffer
     *
     * @return int<0, max>|null
     */
    private function indexOfBrace(array $buffer): ?int
    {
        foreach ($buffer as $offset => $token) {
            if ($token->text === '{') {
                return $offset;
            }
        }

        return null;
    }

    /**
     * @param list<\PhpToken> $buffer
     * @param int<0, max> $braceOffset
     *
     * @return list<array{non-empty-string, non-empty-string|null}>
     */
    private function parseGroupImports(array $buffer, int $braceOffset): array
    {
        $prefix = $this->readName(\array_slice($buffer, 0, $braceOffset));

        if ($prefix === '') {
            return [];
        }

        $imports = [];
        $member = [];

        foreach (\array_slice($buffer, $braceOffset + 1) as $token) {
            if ($token->text === '}') {
                break;
            }

            if ($token->text === ',') {
                $import = $this->parseImport($member, $prefix);

                if ($import !== null) {
                    $imports[] = $import;
                }

                $member = [];

                continue;
            }

            $member[] = $token;
        }

        $import = $this->parseImport($member, $prefix);

        if ($import !== null) {
            $imports[] = $import;
        }

        return $imports;
    }

    /**
     * @param list<\PhpToken> $tokens
     * @param non-empty-string|null $prefix
     *
     * @return array{non-empty-string, non-empty-string|null}|null
     */
    private function parseImport(array $tokens, ?string $prefix = null): ?array
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

        return $this->lookupUseStatements($tokens);
    }
}
