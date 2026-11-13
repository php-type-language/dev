<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Exception;

/**
 * An optional package required by the setup is not installed.
 */
final class MissingDependencyException extends ReaderException
{
    /**
     * @param non-empty-string $package
     * @param non-empty-string $purpose
     */
    public static function becausePackageIsNotInstalled(string $package, string $purpose): self
    {
        $message = \sprintf(
            '%s requires the "%s" package, run "composer require %s"',
            $purpose,
            \addcslashes($package, '"'),
            $package,
        );

        return new self($message);
    }
}
