<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Description;

/**
 * Representation of any entry that contains a description.
 */
interface DescriptionProviderInterface extends OptionalDescriptionProviderInterface
{
    /**
     * Gets a description object which can be represented as a {@see string}
     * and contains additional information.
     *
     * @readonly
     */
    public DescriptionInterface $description {
        get;
    }
}
