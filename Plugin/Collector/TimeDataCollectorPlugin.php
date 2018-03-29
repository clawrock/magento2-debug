<?php

namespace ClawRock\Debug\Plugin\Collector;

use Magento\Framework\Profiler;

class TimeDataCollectorPlugin
{
    /**
     * @var \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver
     */
    private $stopwatchDriver;

    public function __construct(
        \ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver $stopwatchDriver
    ) {
        $this->stopwatchDriver = $stopwatchDriver;
    }

    public function beforeLaunch(\Magento\Framework\App\Http $subject)
    {
        Profiler::reset();
        Profiler::add($this->stopwatchDriver);
        Profiler::start($this->stopwatchDriver::ROOT_EVENT);
    }
}
