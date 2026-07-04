<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Reference;

final readonly class UriReference implements ReferenceInterface
{
    public bool $isExternal;

    public function __construct(
        /**
         * The URI MUST be complete and well-formed as specified in RFC2396.
         *
         * @link https://www.ietf.org/rfc/rfc2396.txt
         * @var non-empty-string
         */
        public string $uri,
    ) {
        $this->isExternal = true;
    }

    public function __toString(): string
    {
        return $this->uri;
    }
}
