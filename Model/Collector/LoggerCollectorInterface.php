<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\LoggableInterface;

interface LoggerCollectorInterface
{
    public function log(LoggableInterface $value): LoggerCollectorInterface;
}
