<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Content;

/**
 * @template-covariant T of mixed
 *
 * @template-extends OptionalReaderInterface<T>
 */
interface ReaderInterface extends OptionalReaderInterface
{
    /**
     * @return T
     */
    public function __invoke(Stream $stream): mixed;
}
