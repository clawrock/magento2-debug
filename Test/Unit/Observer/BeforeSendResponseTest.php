<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Observer\BeforeSendResponse;
use PHPUnit\Framework\TestCase;

class BeforeSendResponseTest extends TestCase
{
    /** @var \ClawRock\Debug\Helper\Config&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\Config $configMock;
    /** @var \ClawRock\Debug\Model\Profiler&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Profiler $profilerMock;
    /** @var \Magento\Framework\HTTP\PhpEnvironment\Request&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\HTTP\PhpEnvironment\Request $requestMock;
    /** @var \Magento\Framework\HTTP\PhpEnvironment\Response&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\HTTP\PhpEnvironment\Response $responseMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    private \ClawRock\Debug\Observer\BeforeSendResponse $observer;

    protected function setUp(): void
    {
        $this->configMock = $this->getMockBuilder(\ClawRock\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profilerMock = $this->getMockBuilder(\ClawRock\Debug\Model\Profiler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\HTTP\PhpEnvironment\Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseMock = $this->getMockBuilder(\Magento\Framework\HTTP\PhpEnvironment\Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getRequest', 'getResponse'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new BeforeSendResponse($this->configMock, $this->profilerMock);
    }

    public function testNoExecute(): void
    {
        $this->observerMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->observerMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->responseMock);

        $this->requestMock->expects($this->atLeastOnce())->method('getModuleName')->willReturn('debug');

        $this->profilerMock->expects($this->never())
            ->method('run')
            ->with($this->requestMock, $this->responseMock);

        $this->observer->execute($this->observerMock);
    }

    public function testExecute(): void
    {
        $this->observerMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->observerMock->expects($this->once())
            ->method('getResponse')
            ->willReturn($this->responseMock);

        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);

        $this->requestMock->expects($this->atLeastOnce())->method('getModuleName')->willReturn('cms');

        $this->profilerMock->expects($this->once())
            ->method('run')
            ->with($this->requestMock, $this->responseMock);

        $this->observer->execute($this->observerMock);
    }
}
