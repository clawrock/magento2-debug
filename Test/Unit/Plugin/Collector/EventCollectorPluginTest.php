<?php

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\EventCollectorPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class EventCollectorPluginTest extends TestCase
{
    private $eventCollectorMock;

    private $observerMock;

    private $eventMock;

    private $proceedMock;

    private $subjectMock;

    private $plugin;

    protected function setUp()
    {
        $this->eventCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\EventCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventMock = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->proceedMock = function () {
            return true;
        };

        $this->subjectMock = $this->getMockBuilder(\Magento\Framework\Event\Invoker\InvokerDefault::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = (new ObjectManager($this))->getObject(EventCollectorPlugin::class, [
            'eventCollector' => $this->eventCollectorMock,
        ]);
    }

    public function testAroundDispatch()
    {
        $configuration = ['name' => 'observer_name', 'instance' => 'observer_instance'];

        $this->eventCollectorMock->expects($this->once())->method('log');
        $this->observerMock->expects($this->once())->method('getEvent')->willReturn($this->eventMock);
        $this->eventMock->expects($this->once())->method('getName')->willReturn('event_name');

        $this->plugin->aroundDispatch($this->subjectMock, $this->proceedMock, $configuration, $this->observerMock);
    }
}
