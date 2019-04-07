<?php

namespace ClawRock\Debug\Test\Unit\Plugin\PageCache;

use ClawRock\Debug\Plugin\PageCache\KernelPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class KernelPluginTest extends TestCase
{
    private $httpStorageMock;

    private $subjectMock;

    private $plugin;

    protected function setUp()
    {
        $this->httpStorageMock = $this->getMockBuilder(\ClawRock\Debug\Model\Storage\HttpStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subjectMock = $this->getMockBuilder(\Magento\Framework\App\PageCache\Kernel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = (new ObjectManager($this))->getObject(KernelPlugin::class, [
            'httpStorage' => $this->httpStorageMock,
        ]);
    }

    public function testAfterLoad()
    {
        $this->httpStorageMock->expects($this->never())->method('markAsFPCRequest');
        $this->assertFalse($this->plugin->afterLoad($this->subjectMock, false));
    }

    public function testAfterLoadFPC()
    {
        $result = 'cached_content';
        $this->httpStorageMock->expects($this->once())->method('markAsFPCRequest');
        $this->assertEquals($result, $this->plugin->afterLoad($this->subjectMock, $result));
    }
}
