<?php
declare(strict_types=1);

namespace ClawRock\Debug\Plugin\Collector;

use Magento\Framework\Profiler;

class TimeCollectorPlugin
{
    private \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver $stopwatchDriver;

    public function __construct(
        \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver $stopwatchDriver
    ) {
        $this->stopwatchDriver = $stopwatchDriver;
    }

    public function beforeLaunch(): void
    {
        Profiler::reset();
        Profiler::add($this->stopwatchDriver);
        Profiler::start($this->stopwatchDriver::ROOT_EVENT);
    }
}
