<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Cache;

use ClawRock\Debug\Controller\Cache\Clean;

class CleanTest extends CacheControllerTestCase
{
    private \ClawRock\Debug\Controller\Cache\Clean $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new Clean($this->resultFactoryMock, $this->requestMock, $this->cacheManagerMock);
    }

    public function testExecute(): void
    {
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->willReturn($this->resultMock);
        $this->requestMock->expects($this->once())->method('getParam')
            ->with('type')
            ->willReturn(null);
        $this->cacheManagerMock->expects($this->once())->method('getAvailableTypes')
            ->willReturn(['cache_type_1', 'cache_type_2']);
        $this->cacheManagerMock->expects($this->once())->method('clean')->with(['cache_type_1', 'cache_type_2']);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }
}
