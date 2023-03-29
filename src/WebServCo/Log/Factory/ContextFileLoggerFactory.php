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

final class ContextFileLoggerFactory implements LoggerFactoryInterface
{
    public function __construct(private string $logDirectory)
    {
        if (!is_writable($logDirectory)) {
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
