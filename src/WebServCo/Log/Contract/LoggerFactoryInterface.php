<?php

declare(strict_types=1);

namespace WebServCo\Log\Contract;

use Psr\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    public function createLogger(string $channel): LoggerInterface;
}
