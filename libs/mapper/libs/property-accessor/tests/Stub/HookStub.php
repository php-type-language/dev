<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Tests\Stub;

final class HookStub
{
    /**
     * Virtual, computed, read-only property (no backing store).
     */
    public string $virtualReadOnly {
        get => 'computed';
    }

    /**
     * Non-virtual property with hooks over its own backing store.
     */
    public string $hooked = 'raw-init' {
        get => 'via-get-hook';
        set (string $value) => $this->hooked = 'via-set-hook:' . $value;
    }
}
