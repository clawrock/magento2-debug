<?php

namespace ClawRock\Debug\Test\Unit\Plugin\Collector;

use ClawRock\Debug\Plugin\Collector\CacheCollectorPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class CacheCollectorPluginTest extends TestCase
{
    private $cacheCollectorMock;

    private $proceedMock;

    private $subjectMock;

    private $plugin;

    protected function setUp()
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

        $this->plugin = (new ObjectManager($this))->getObject(CacheCollectorPlugin::class, [
            'cacheCollector' => $this->cacheCollectorMock,
        ]);
    }

    public function testAroundLoad()
    {
        $identifier = 'cache_id';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundLoad($this->subjectMock, $this->proceedMock, $identifier));
    }

    public function testAroundSave()
    {
        $identifier = 'cache_id';
        $tags = ['cache_tag'];
        $data = 'cached data';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundSave($this->subjectMock, $this->proceedMock, $data, $identifier, $tags));
    }

    public function testAroundRemove()
    {
        $identifier = 'cache_id';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundRemove($this->subjectMock, $this->proceedMock, $identifier));
    }

    public function testAroundClean()
    {
        $identifier = 'cache_id';

        $this->cacheCollectorMock->expects($this->once())->method('log');

        $this->assertTrue($this->plugin->aroundClean($this->subjectMock, $this->proceedMock, $identifier));
    }
}
