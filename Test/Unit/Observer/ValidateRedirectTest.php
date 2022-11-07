<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Observer;

use ClawRock\Debug\Observer\ValidateRedirect;
use PHPUnit\Framework\TestCase;

class ValidateRedirectTest extends TestCase
{
    /** @var \ClawRock\Debug\Model\Session&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Session $sessionMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    /** @var \Magento\Framework\App\RequestInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\RequestInterface $requestMock;
    private \ClawRock\Debug\Observer\ValidateRedirect $observer;

    protected function setUp(): void
    {
        $this->sessionMock = $this->getMockBuilder(\ClawRock\Debug\Model\Session::class)
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

        $this->observer = new ValidateRedirect($this->sessionMock);
    }

    public function testExecute(): void
    {
        $this->sessionMock->expects($this->once())->method('getData')->willReturn('1');
        $this->observerMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->requestMock->expects($this->once())->method('setParam');

        $this->observer->execute($this->observerMock);
    }
}
