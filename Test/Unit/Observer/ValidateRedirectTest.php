<?php

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Observer\ValidateRedirect;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ValidateRedirectTest extends TestCase
{
    private $sessionMock;

    private $observerMock;

    private $requestMock;

    private $observer;

    protected function setUp()
    {
        $this->sessionMock = $this->getMockBuilder(\ClawRock\Debug\Model\Session\Proxy::class)
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['setParam'])
            ->getMockForAbstractClass();

        $this->observer = (new ObjectManager($this))->getObject(ValidateRedirect::class, [
            'session' => $this->sessionMock,
        ]);
    }

    public function testExecute()
    {
        $this->sessionMock->expects($this->once())->method('getData')->willReturn('1');
        $this->observerMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->requestMock->expects($this->once())->method('setParam');

        $this->observer->execute($this->observerMock);
    }
}
