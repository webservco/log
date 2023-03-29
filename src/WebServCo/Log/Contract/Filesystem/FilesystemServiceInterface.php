<?php

declare(strict_types=1);

namespace WebServCo\Log\Contract\Filesystem;

/**
 * Filesystem helper interface.
 *
 * Helps to customize log directory structure.
 */
interface FilesystemServiceInterface
{
    public function getContextFilePath(string $channel, string $logId): string;

    public function getLogFilePath(string $channel): string;

    public function write(string $path, string $data): bool;
}
