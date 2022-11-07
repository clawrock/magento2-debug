<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Observer\AllowedIP;
use PHPUnit\Framework\TestCase;

class AllowedIPTest extends TestCase
{
    /** @var \ClawRock\Debug\Helper\Config&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Config $configMock;
    /** @var \Magento\Framework\App\Request\Http&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\Request\Http $requestMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    private \ClawRock\Debug\Observer\AllowedIP $observer;

    protected function setUp(): void
    {
        $this->configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new AllowedIP($this->configMock);
    }

    public function testExecuteAllowed(): void
    {
        $this->configMock->expects($this->once())->method('isAllowedIP')->willReturn(true);
        $this->observerMock->expects($this->never())->method('getRequest');
        $this->observer->execute($this->observerMock);
    }

    public function testExecuteNotAllowed(): void
    {
        $this->configMock->expects($this->once())->method('isAllowedIP')->willReturn(false);
        $this->observerMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->requestMock->expects($this->once())->method('initForward');
        $this->requestMock->expects($this->once())->method('setControllerName')->with('noroute');
        $this->requestMock->expects($this->once())->method('setModuleName')->with('cms');
        $this->requestMock->expects($this->once())->method('setActionName')->with('index');
        $this->requestMock->expects($this->once())->method('setDispatched')->with(false);

        $this->observer->execute($this->observerMock);
    }
}
