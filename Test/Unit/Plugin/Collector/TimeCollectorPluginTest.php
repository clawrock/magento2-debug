<?php

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\TimeCollectorPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class TimeCollectorPluginTest extends TestCase
{
    public function testBeforeLaunch()
    {
        $stopwatchDriverMock = $this->getMockBuilder(\ClawRock\Debug\Model\Profiler\Driver\StopwatchDriver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subjectMock = $this->getMockBuilder(\Magento\Framework\App\Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $plugin = (new ObjectManager($this))->getObject(TimeCollectorPlugin::class, [
            'stopwatchDriver' => $stopwatchDriverMock,
        ]);

        $plugin->beforeLaunch($subjectMock);

        $this->assertTrue(\Magento\Framework\Profiler::isEnabled());
    }
}
