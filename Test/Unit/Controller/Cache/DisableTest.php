<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Controller\Cache;

use ClawRock\Debug\Controller\Cache\Disable;

class DisableTest extends CacheControllerTestCase
{
    private \ClawRock\Debug\Controller\Cache\Disable $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new Disable($this->resultFactoryMock, $this->requestMock, $this->cacheManagerMock);
    }

    public function testExecute(): void
    {
        $this->resultFactoryMock->expects($this->once())->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->willReturn($this->resultMock);
        $this->requestMock->expects($this->once())->method('getParam')->with('type')->willReturn('cache_type');
        $this->cacheManagerMock->expects($this->once())->method('setEnabled')->with(['cache_type'], false);

        $this->assertInstanceOf(\Magento\Framework\Controller\ResultInterface::class, $this->controller->execute());
    }
}
