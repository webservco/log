<?php

declare(strict_types=1);

namespace WebServCo\Log\Factory;

use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use WebServCo\Log\Contract\LoggerFactoryInterface;
use WebServCo\Log\Service\Context\ContextVarExportService;
use WebServCo\Log\Service\ContextFileLogger;
use WebServCo\Log\Service\Filesystem\FilesystemWithDateService;
use WebServCo\Log\Service\LevelService;

use function is_writable;
use function rtrim;

use const DIRECTORY_SEPARATOR;

final class ContextFileLoggerFactory implements LoggerFactoryInterface
{
    public function __construct(private string $logDirectory)
    {
        // Make sure path contains trailing slash (trim + add back).
        $this->logDirectory = rtrim($this->logDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_writable($this->logDirectory)) {
            throw new OutOfBoundsException('Log directory is not writable.');
        }
    }

    public function createLogger(string $channel): LoggerInterface
    {
        return new ContextFileLogger(
            $channel,
            new ContextVarExportService(),
            new FilesystemWithDateService($this->logDirectory),
            new LevelService(),
        );
    }
}
