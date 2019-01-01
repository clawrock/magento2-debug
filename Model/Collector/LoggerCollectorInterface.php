<?php

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Logger\DataLogger;
use ClawRock\Debug\Logger\LoggableInterface;

interface LoggerCollectorInterface
{
    public function log(LoggableInterface $value): LoggerCollectorInterface;
}
