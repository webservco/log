<?php

declare(strict_types=1);

namespace WebServCo\Log\Service\Filesystem;

use DateTimeImmutable;
use OutOfBoundsException;
use WebServCo\File\Service\File\FileService;
use WebServCo\Log\Contract\Filesystem\FilesystemServiceInterface;

use function is_dir;
use function is_readable;
use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Filesystem helper implementation using separate directories for each day.
 */
final class FilesystemWithDateService implements FilesystemServiceInterface
{
    public function __construct(private string $baseDirectoryPath, private FileService $fileService)
    {
        // Make sure path contains trailing slash (trim + add back).
        $this->baseDirectoryPath = rtrim($this->baseDirectoryPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($this->baseDirectoryPath)) {
            throw new OutOfBoundsException('Base log directory path does not exist, or is not a directory.');
        }

        if (!is_readable($this->baseDirectoryPath)) {
            throw new OutOfBoundsException('Base log directory path is not readable.');
        }
    }

    public function getContextFilePath(string $channel, string $logId): string
    {
        return sprintf(
            '%s%s%scontext-%s%s%s.context',
            $this->baseDirectoryPath,
            $this->getSubdirectoryName(),
            DIRECTORY_SEPARATOR,
            $channel,
            DIRECTORY_SEPARATOR,
            $logId,
        );
    }

    public function getLogFilePath(string $channel): string
    {
        return sprintf(
            '%s%s%s%s.log',
            $this->baseDirectoryPath,
            $this->getSubdirectoryName(),
            DIRECTORY_SEPARATOR,
            $channel,
        );
    }

    public function write(string $data, string $path): bool
    {
        return $this->fileService->writeDataToFilePath($data, $path);
    }

    private function getSubdirectoryName(): string
    {
        $date = new DateTimeImmutable();

        return $date->format('Ymd');
    }
}
