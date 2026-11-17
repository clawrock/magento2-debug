<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\EventCollectorPlugin;
use PHPUnit\Framework\TestCase;

class EventCollectorPluginTest extends TestCase
{
    /** @var \ClawRock\Debug\Helper\Config&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Config $configMock;
    /** @var \ClawRock\Debug\Model\Collector\EventCollector&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Collector\EventCollector $eventCollectorMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    /** @var \Magento\Framework\Event&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event $eventMock;
    private \Closure $proceedMock;
    /** @var \Magento\Framework\Event\Invoker\InvokerDefault&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Invoker\InvokerDefault $subjectMock;
    private \ClawRock\Debug\Plugin\Collector\EventCollectorPlugin $plugin;

    protected function setUp(): void
    {
        $this->configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock->method('isEnabled')->willReturn(true);

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

        $this->plugin = new EventCollectorPlugin($this->configMock, $this->eventCollectorMock);
    }

    public function testAroundDispatch(): void
    {
        $configuration = ['name' => 'observer_name', 'instance' => 'observer_instance'];

        $this->eventCollectorMock->expects($this->once())->method('log');
        $this->observerMock->expects($this->once())->method('getEvent')->willReturn($this->eventMock);
        $this->eventMock->expects($this->once())->method('getName')->willReturn('event_name');

        $this->plugin->aroundDispatch($this->subjectMock, $this->proceedMock, $configuration, $this->observerMock);
    }

    public function testAroundDispatchWhenConfigurationHasNoInstance(): void
    {
        $configuration = ['name' => 'observer_name'];
        $proceedCalled = false;
        $proceed = function () use (&$proceedCalled): void {
            $proceedCalled = true;
        };

        $this->eventCollectorMock->expects($this->never())->method('log');
        $this->observerMock->expects($this->never())->method('getEvent');

        $this->plugin->aroundDispatch($this->subjectMock, $proceed, $configuration, $this->observerMock);

        $this->assertTrue($proceedCalled);
    }
}
