<?php
declare(strict_types=1);

namespace ClawRock\Debug\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = Logger::INFO;

    protected $fileName = '/var/log/profiler.log';
}
