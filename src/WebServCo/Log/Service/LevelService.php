<?php

declare(strict_types=1);

namespace WebServCo\Log\Service;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

use function is_scalar;

final class LevelService
{
    /**
     * @param mixed $level
     */
    public function validateLevel($level): bool
    {
        switch ($level) {
            // "System is unusable."
            case LogLevel::EMERGENCY:
            // "Action must be taken immediately."
            case LogLevel::ALERT:
            // "Critical conditions."
            case LogLevel::CRITICAL:
            // "Runtime errors that do not require immediate action but should typically be logged and monitored."
            case LogLevel::ERROR:
            // "Exceptional occurrences that are not errors."
            case LogLevel::WARNING:
            // "Normal but significant events."
            case LogLevel::NOTICE:
            // "Interesting events."
            case LogLevel::INFO:
            // "Detailed debug information."
            case LogLevel::DEBUG:
                return true;
            default:
                throw new InvalidArgumentException('Invalid level specified.');
        }
    }

    /**
     * @param mixed $level
     */
    public function toString($level): string
    {
        if (is_scalar($level)) {
            return (string) $level;
        }

        throw new InvalidArgumentException('Invalid level type specified.');
    }
}
