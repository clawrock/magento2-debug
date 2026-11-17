<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\TimeCollectorPlugin;
use PHPUnit\Framework\TestCase;

class TimeCollectorPluginTest extends TestCase
{
    public function testBeforeLaunch(): void
    {
        $configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->method('isEnabled')->willReturn(true);
        $stopwatchDriverMock = $this->createMock(\ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver::class);
        $plugin = new TimeCollectorPlugin($configMock, $stopwatchDriverMock);
        $plugin->beforeLaunch();

        $this->assertTrue(\Magento\Framework\Profiler::isEnabled());
    }
}
