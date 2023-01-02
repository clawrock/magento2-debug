<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Model\Profiler;
use ClawRock\Debug\Observer\DebugHandle;
use PHPUnit\Framework\TestCase;

class DebugHandleTest extends TestCase
{
    /** @var \Magento\Framework\View\LayoutInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\LayoutInterface $layoutMock;
    /** @var \Magento\Framework\View\Layout\ProcessorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\Layout\ProcessorInterface $updateMock;
    /** @var \ClawRock\Debug\Helper\Config&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Config $configMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    private \ClawRock\Debug\Observer\DebugHandle $observer;

    protected function setUp(): void
    {
        $this->layoutMock = $this->getMockForAbstractClass(\Magento\Framework\View\LayoutInterface::class);

        $this->updateMock = $this->getMockForAbstractClass(\Magento\Framework\View\Layout\ProcessorInterface::class);

        $this->configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getLayout', 'getFullActionName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new DebugHandle($this->configMock);
    }

    public function testExecute(): void
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->observerMock->expects($this->once())
            ->method('getFullActionName')
            ->willReturn(Profiler::TOOLBAR_FULL_ACTION_NAME);

        $this->observerMock->expects($this->exactly(2))->method('getLayout')->willReturn($this->layoutMock);
        $this->layoutMock->expects($this->exactly(2))->method('getUpdate')->willReturn($this->updateMock);
        $this->updateMock->expects($this->once())->method('addHandle');
        $this->updateMock->expects($this->once())->method('removeHandle');

        $this->observer->execute($this->observerMock);
    }
}
