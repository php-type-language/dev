<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Naming;

use TypeLang\Mapper\Naming\Internal\WordSplitter;

/**
 * Built-in naming strategies. The value of each case is the name of the
 * style written in that style, as in Serde `rename_all`.
 */
enum NamingStrategy: string implements NamingStrategyInterface
{
    /**
     * Lowercase first word, capitalized following words: `userId`.
     */
    case CamelCase = 'camelCase';

    /**
     * Capitalized words: `UserId`.
     */
    case PascalCase = 'PascalCase';

    /**
     * Lowercase words joined by underscore: `user_id`.
     */
    case SnakeCase = 'snake_case';

    /**
     * Uppercase words joined by underscore: `USER_ID`.
     */
    case ScreamingSnakeCase = 'SCREAMING_SNAKE_CASE';

    /**
     * Lowercase words joined by hyphen: `user-id`.
     */
    case KebabCase = 'kebab-case';

    private const UPPERCASE_LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private const LOWERCASE_LETTERS = 'abcdefghijklmnopqrstuvwxyz';

    public function rename(string $name): string
    {
        $words = WordSplitter::split($name);

        if ($words === []) {
            return $name;
        }

        return match ($this) {
            self::CamelCase => self::toLowerCase(\array_shift($words))
                . \implode('', \array_map(self::capitalize(...), $words)),
            self::PascalCase => \implode('', \array_map(self::capitalize(...), $words)),
            self::SnakeCase => self::toLowerCase(\implode('_', $words)),
            self::ScreamingSnakeCase => self::toUpperCase(\implode('_', $words)),
            self::KebabCase => self::toLowerCase(\implode('-', $words)),
        };
    }

    /**
     * @param non-empty-string $word
     * @return non-empty-string
     */
    private static function toLowerCase(string $word): string
    {
        return \strtr($word, self::UPPERCASE_LETTERS, self::LOWERCASE_LETTERS);
    }

    /**
     * @param non-empty-string $word
     * @return non-empty-string
     */
    private static function toUpperCase(string $word): string
    {
        return \strtr($word, self::LOWERCASE_LETTERS, self::UPPERCASE_LETTERS);
    }

    /**
     * @param non-empty-string $word
     * @return non-empty-string
     */
    private static function capitalize(string $word): string
    {
        return self::toUpperCase($word[0]) . \strtr(
            \substr($word, 1),
            self::UPPERCASE_LETTERS,
            self::LOWERCASE_LETTERS,
        );
    }
}
