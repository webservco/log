<?php

declare(strict_types=1);

namespace WebServCo\Log\Service;

use DateTimeImmutable;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;
use WebServCo\Log\Contract\Context\ContextServiceInterface;
use WebServCo\Log\Contract\Filesystem\FilesystemServiceInterface;

use function sprintf;

use const PHP_EOL;

/**
 * Log to file, with separate context information.
 */
final class ContextFileLogger extends AbstractLogger implements LoggerInterface
{
    private string $channel;
    private ContextServiceInterface $contextService;
    private FilesystemServiceInterface $filesystemService;
    private LevelService $levelService;
    public function __construct(string $channel, ContextServiceInterface $contextService, FilesystemServiceInterface $filesystemService, LevelService $levelService)
    {
        $this->channel = $channel;
        $this->contextService = $contextService;
        $this->filesystemService = $filesystemService;
        $this->levelService = $levelService;
    }
    /**
     * Main log action.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowArrayTypeHintSyntax.DisallowedArrayTypeHintSyntax
     * @param mixed[] $context
     * @phpcs:enable
     * @param string|\Stringable $message
     * @param mixed $level
     */
    public function log($level, $message, array $context = []): void
    {
        $this->levelService->validateLevel($level);

        $logId = $this->createLogId();

        $contextResult = $this->handleContext($logId, $context);

        $logPath = $this->filesystemService->getLogFilePath($this->channel);
        $logData = sprintf(
            // [logId] level message [context]
            '[%s] %s %s%s%s',
            $logId,
            $this->levelService->toString($level),
            (string) $message,
            $contextResult ? ' [context]' : '',
            PHP_EOL,
        );

        $this->filesystemService->write($logPath, $logData);
    }

    private function createLogId(): string
    {
        $date = new DateTimeImmutable();

        return $date->format('Ymd.His.u');
    }

    /**
     * Handle context logging.
     *
     * PSR allows anything in $context, so exceptionally, we use "mixed".
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowArrayTypeHintSyntax.DisallowedArrayTypeHintSyntax
     * @param mixed[] $context
     * @phpcs:enable
     */
    private function handleContext(string $logId, array $context = []): bool
    {
        if ($context === []) {
            // Context is empty.
            return false;
        }

        $contextPath = $this->filesystemService->getContextFilePath($this->channel, $logId);

        $contextData = $this->contextService->toString($context);

        return $this->filesystemService->write($contextPath, $contextData);
    }
}
