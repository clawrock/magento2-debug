<?php

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Observer\BeforeSendResponse;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class BeforeSendResponseTest extends TestCase
{
    private $configMock;

    private $profilerMock;

    private $requestMock;

    private $responseMock;

    private $observerMock;

    private $observer;

    protected function setUp()
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

        $this->observer = (new ObjectManager($this))->getObject(BeforeSendResponse::class, [
            'config' => $this->configMock,
            'profiler' => $this->profilerMock,
        ]);
    }

    public function testNoExecute()
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

    public function testExecute()
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
