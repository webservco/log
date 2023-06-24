<?php

declare(strict_types=1);

namespace WebServCo\Log\Service\Filesystem;

use DateTimeImmutable;
use OutOfBoundsException;
use WebServCo\Log\Contract\Filesystem\FilesystemServiceInterface;

use function file_put_contents;
use function is_dir;
use function is_readable;
use function mkdir;
use function pathinfo;
use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;
use const FILE_APPEND;
use const PATHINFO_DIRNAME;

/**
 * Filesystem helper implementation using separate directories for each day.
 */
final class FilesystemWithDateService implements FilesystemServiceInterface
{
    private string $baseDirectoryPath;
    public function __construct(string $baseDirectoryPath)
    {
        $this->baseDirectoryPath = $baseDirectoryPath;
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

    public function write(string $path, string $data): bool
    {
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $dirResult = $this->createDirectoryIfNotExists($directory);
        if ($dirResult === false) {
            throw new OutOfBoundsException('Error creating log directory.');
        }

        $fileResult = file_put_contents($path, $data, FILE_APPEND);
        if ($fileResult === false) {
            throw new OutOfBoundsException('Error writing log file.');
        }

        return true;
    }

    private function createDirectoryIfNotExists(string $directory): bool
    {
        if (is_dir($directory)) {
            // Directory already exists.
            return true;
        }

        return mkdir(
            $directory,
            // permissions
            0775,
            // recursive
            true,
            // context
        );
    }

    private function getSubdirectoryName(): string
    {
        $date = new DateTimeImmutable();

        return $date->format('Ymd');
    }
}
