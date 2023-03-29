<?php

declare(strict_types=1);

namespace WebServCo\Log\Contract\Context;

interface ContextServiceInterface
{
    /**
     * Convert context to string.
     *
     * Use case: write in a file.
     *
     * PSR allows anything in $context, so we use "mixed" as an exception.
     * phpcs:ignore SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<int|string,mixed> $context
     */
    public function toString(array $context): string;
}
