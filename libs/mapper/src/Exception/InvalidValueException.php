<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Exception;

use TypeLang\Mapper\Diagnostics\Exception\ViolationException;
use TypeLang\Mapper\Diagnostics\Path\Path;
use TypeLang\Mapper\Diagnostics\Path\Segment\KeySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\SegmentInterface;
use TypeLang\Mapper\Diagnostics\Violation;
use TypeLang\Mapper\Diagnostics\ViolationCode;

/**
 * Value cannot be converted by the requested type. A type throws it with
 * the path relative to its own value, every composite type the error passes
 * through prepends its segment.
 */
final class InvalidValueException extends ViolationException implements MapperExceptionInterface
{
    /**
     * @param non-empty-string $expected
     */
    public static function becauseInvalidType(string $expected, mixed $value): self
    {
        return self::fromViolation(
            new Violation(ViolationCode::InvalidType, ['expected' => $expected], new Path(), $value),
        );
    }

    /**
     * The path of the error points to the key itself.
     *
     * @param non-empty-string $expected
     */
    public static function becauseInvalidKey(string $expected, int|string $key): self
    {
        return self::fromViolation(new Violation(
            ViolationCode::InvalidKey,
            ['expected' => $expected],
            new Path([new KeySegment($key)]),
            $key,
        ));
    }

    /**
     * The path of the error points to the place of the field.
     *
     * @param non-empty-string $field
     */
    public static function becauseFieldIsMissing(string $field, SegmentInterface $segment): self
    {
        return self::fromViolation(
            new Violation(ViolationCode::MissingField, ['field' => $field], new Path([$segment])),
        );
    }

    /**
     * The path of the error points to the place of the field.
     */
    public static function becauseFieldIsUnexpected(string $field, SegmentInterface $segment, mixed $value): self
    {
        return self::fromViolation(
            new Violation(ViolationCode::UnexpectedField, ['field' => $field], new Path([$segment]), $value),
        );
    }

    /**
     * @param non-empty-string $expected
     * @param non-empty-string $check
     */
    public static function becauseCheckFailed(string $expected, string $check, mixed $value): self
    {
        return self::fromViolation(new Violation(
            ViolationCode::FailedCheck,
            ['expected' => $expected, 'check' => $check],
            new Path(),
            $value,
        ));
    }

    /**
     * Returns the error with the segment prepended to the paths of its
     * violations. An error of another kind becomes one violation of the
     * value at the segment.
     */
    public static function wrapWithPath(\Throwable $error, SegmentInterface $segment, mixed $value): self
    {
        if (!$error instanceof self) {
            $error = self::fromViolation(
                new Violation(ViolationCode::InvalidValue, ['message' => $error->getMessage()], new Path(), $value),
                $error,
            );
        }

        $previous = $error->getPrevious();

        $violations = [];

        foreach ($error->violations as $violation) {
            $violations[] = new Violation(
                $violation->code,
                $violation->parameters,
                new Path([$segment, ...$violation->path->segments]),
                $violation->value,
                $violation->template,
            );
        }

        return new self($violations, self::formatSummary($violations), $previous);
    }

    /**
     * Returns the error of the violations of the collected error and then
     * of the given one, for a composite type to go on with its other
     * values.
     *
     * @param int<1, max> $limit
     * @throws self in case of the limit of the violations is reached, with
     *         the violations cut to the limit
     */
    public static function collect(?self $collected, self $error, int $limit): self
    {
        if ($collected !== null) {
            $violations = [...$collected->violations, ...$error->violations];
            $error = new self($violations, self::formatSummary($violations), $collected->getPrevious());
        }

        if (\count($error->violations) < $limit) {
            return $error;
        }

        if (\count($error->violations) === $limit) {
            throw $error;
        }

        /** @var non-empty-list<Violation> */
        $violations = \array_slice($error->violations, 0, $limit);

        throw new self($violations, self::formatSummary($violations), $error->getPrevious());
    }

    /**
     * Returns the error with the message of each violation written by the
     * template, such as a text of the user for a property, the error as is
     * in case of the template is {@see null}.
     *
     * @param non-empty-string|null $template
     */
    public function withTemplate(?string $template): self
    {
        if ($template === null) {
            return $this;
        }

        $violations = [];

        foreach ($this->violations as $violation) {
            $violations[] = $violation->withTemplate($template);
        }

        return new self($violations, self::formatSummary($violations), $this->getPrevious());
    }

    private static function fromViolation(Violation $violation, ?\Throwable $previous = null): self
    {
        return new self([$violation], self::formatSummary([$violation]), $previous);
    }
}
