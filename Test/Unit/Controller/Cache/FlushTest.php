<?php

namespace ClawRock\Debug\Test\Unit\Controller\Cache;

use ClawRock\Debug\Controller\Cache\Flush;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class FlushTest extends TestCase
{
    private $resultMock;

    private $resultFactoryMock;

    private $requestMock;

    private $contextMock;

    private $cacheManagerMock;

    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultMock = $this->getMockForAbstractClass(\Magento\Framework\Controller\ResultInterface::class);

        $this->requestMock = $this->getMockForAbstractClass(\Magento\Framework\App\RequestInterface::class);

        $this->contextMock = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->once())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);

        $this->cacheManagerMock = $this->getMockBuilder(\Magento\Framework\App\Cache\Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = (new ObjectManager($this))->getObject(Flush::class, [
            'context' => $this->contextMock,
            'cacheManager' => $this->cacheManagerMock,
        ]);
    }

    public function testExecute()
    {
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->willReturn($this->resultMock);
        $this->requestMock->expects($this->once())->method('getParam')
            ->with('type')
            ->willReturn(null);
        $this->cacheManagerMock->expects($this->once())->method('getAvailableTypes')
            ->willReturn(['cache_type1', 'cache_type2']);
        $this->cacheManagerMock->expects($this->once())->method('flush')
            ->with(['cache_type1', 'cache_type2']);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }
}
