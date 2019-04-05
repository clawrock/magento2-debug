<?php

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Observer\AllowedIP;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class AllowedIPTest extends TestCase
{
    private $configMock;

    private $requestMock;

    private $observerMock;

    private $observer;

    protected function setUp()
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

        $this->observer = (new ObjectManager($this))->getObject(AllowedIP::class, [
            'config' => $this->configMock,
        ]);
    }

    public function testExecuteAllowed()
    {
        $this->configMock->expects($this->once())->method('isAllowedIP')->willReturn(true);
        $this->observerMock->expects($this->never())->method('getRequest');
        $this->observer->execute($this->observerMock);
    }

    public function testExecuteNotAllowed()
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
