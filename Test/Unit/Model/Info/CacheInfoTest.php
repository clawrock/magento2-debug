<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\Info;

use ClawRock\Debug\Model\Info\CacheInfo;
use Magento\Framework\Cache\Frontend\Adapter\Symfony\BackendWrapper;
use PHPUnit\Framework\TestCase;

class CacheInfoTest extends TestCase
{
    public function testGetBackendOptionsReturnsEmptyArrayForNonZendBackend(): void
    {
        $backend = $this->getMockBuilder(BackendWrapper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $frontend = $this->getMockBuilder(\Magento\Framework\Cache\FrontendInterface::class)->getMock();
        $frontend->method('getBackend')->willReturn($backend);
        $cache = $this->getMockBuilder(\Magento\Framework\App\Cache::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cache->method('getFrontend')->willReturn($frontend);
        $typeList = $this->getMockBuilder(\Magento\Framework\App\Cache\TypeListInterface::class)->getMock();

        $cacheInfo = new CacheInfo($cache, $typeList);

        $this->assertSame([], $cacheInfo->getBackendOptions());
        $this->assertSame(get_class($backend), $cacheInfo->getBackendClass());
    }
}
