<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\CacheCollectorPlugin;
use PHPUnit\Framework\TestCase;

class CacheCollectorPluginTest extends TestCase
{
    /** @var \ClawRock\Debug\Model\Collector\CacheCollector&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Collector\CacheCollector $cacheCollectorMock;
    private \Closure $proceedMock;
    /** @var \Magento\Framework\App\Cache&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\Cache $subjectMock;
    private \ClawRock\Debug\Plugin\Collector\CacheCollectorPlugin $plugin;

    protected function setUp(): void
    {
        $this->cacheCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\CacheCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->proceedMock = function () {
            return true;
        };

        $this->subjectMock = $this->getMockBuilder(\Magento\Framework\App\Cache::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new CacheCollectorPlugin($this->cacheCollectorMock);
    }

    public function testAroundLoad(): void
    {
        $identifier = 'cache_id';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundLoad($this->subjectMock, $this->proceedMock, $identifier));
    }

    public function testAroundSave(): void
    {
        $identifier = 'cache_id';
        $tags = ['cache_tag'];
        $data = 'cached data';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundSave($this->subjectMock, $this->proceedMock, $data, $identifier, $tags));
    }

    public function testAroundRemove(): void
    {
        $identifier = 'cache_id';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundRemove($this->subjectMock, $this->proceedMock, $identifier));
    }

    public function testAroundClean(): void
    {
        $tags = ['tag'];

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundClean($this->subjectMock, $this->proceedMock, $tags));
    }
}
