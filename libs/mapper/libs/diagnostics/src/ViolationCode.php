<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics;

/**
 * Stable machine-readable codes of mapping violations.
 */
enum ViolationCode: string
{
    /**
     * Value is not of the expected type, including a union where no branch
     * has matched.
     */
    case InvalidType = 'invalid_type';

    /**
     * Key of a collection is not of the expected key type.
     */
    case InvalidKey = 'invalid_key';

    /**
     * Required field is absent, the path points to the field itself.
     */
    case MissingField = 'missing_field';

    /**
     * Field is not expected by the target.
     */
    case UnexpectedField = 'unexpected_field';

    /**
     * Value of the expected type is rejected.
     */
    case InvalidValue = 'invalid_value';

    /**
     * Returns the text with `{{name}}` placeholders of the parameters and
     * `{{value}}` of the rejected value.
     */
    public function getTemplate(): string
    {
        return match ($this) {
            self::InvalidType => 'Value must be of type {{expected}}, but {{value}} given',
            self::InvalidKey => 'Key must be of type {{expected}}, but {{value}} given',
            self::MissingField => 'Required field {{field}} is missing',
            self::UnexpectedField => 'Unexpected field {{field}}',
            self::InvalidValue => '{{message}}',
        };
    }
}
