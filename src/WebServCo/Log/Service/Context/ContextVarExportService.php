<?php

declare(strict_types=1);

namespace WebServCo\Log\Service\Context;

use WebServCo\Log\Contract\Context\ContextServiceInterface;

use function var_export;

final class ContextVarExportService implements ContextServiceInterface
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
    public function toString(array $context): string
    {
        return var_export($context, true);
    }
}
