<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\TimeCollectorPlugin;
use PHPUnit\Framework\TestCase;

class TimeCollectorPluginTest extends TestCase
{
    public function testBeforeLaunch(): void
    {
        $stopwatchDriverMock = $this->createMock(\ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver::class);
        $plugin = new TimeCollectorPlugin($stopwatchDriverMock);
        $plugin->beforeLaunch();

        $this->assertTrue(\Magento\Framework\Profiler::isEnabled());
    }
}
