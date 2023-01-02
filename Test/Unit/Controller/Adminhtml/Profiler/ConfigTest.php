<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Adminhtml\Profiler;

use ClawRock\Debug\Controller\Adminhtml\Profiler\Config;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @var \Magento\Backend\App\Action\Context&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Backend\App\Action\Context $contextMock;
    /** @var \Magento\Framework\Controller\Result\RedirectFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactoryMock;
    /** @var \Magento\Framework\Controller\Result\Redirect&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Controller\Result\Redirect $resultRedirectMock;
    /** @var \Magento\Backend\Model\UrlInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Backend\Model\UrlInterface $urlMock;
    private \ClawRock\Debug\Controller\Adminhtml\Profiler\Config $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlMock = $this->getMockForAbstractClass(UrlInterface::class);

        $this->contextMock->expects($this->once())->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactoryMock);
        $this->contextMock->expects($this->once())->method('getUrl')->willReturn($this->urlMock);

        $this->controller = new Config($this->contextMock);
    }

    public function testExecute(): void
    {
        $this->resultRedirectFactoryMock->expects($this->once())->method('create')
            ->willReturn($this->resultRedirectMock);
        $this->urlMock->expects($this->once())->method('getSecretKey')
            ->with('adminhtml', 'system_config', 'edit')
            ->willReturn('url_with_secret');
        $this->resultRedirectMock->expects($this->once())->method('setPath')
            ->willReturn($this->resultRedirectMock);

        $this->assertInstanceOf(Redirect::class, $this->controller->execute());
    }
}
