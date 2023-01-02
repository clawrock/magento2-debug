<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Plugin\PageCache;

use ClawRock\Debug\Plugin\PageCache\KernelPlugin;
use PHPUnit\Framework\TestCase;

class KernelPluginTest extends TestCase
{
    /** @var \ClawRock\Debug\Model\Storage\HttpStorage&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Storage\HttpStorage $httpStorageMock;
    /** @var \Magento\Framework\App\PageCache\Kernel&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\App\PageCache\Kernel $subjectMock;
    private \ClawRock\Debug\Plugin\PageCache\KernelPlugin $plugin;

    protected function setUp(): void
    {
        $this->httpStorageMock = $this->getMockBuilder(\ClawRock\Debug\Model\Storage\HttpStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subjectMock = $this->getMockBuilder(\Magento\Framework\App\PageCache\Kernel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new KernelPlugin($this->httpStorageMock);
    }

    public function testAfterLoad(): void
    {
        $this->httpStorageMock->expects($this->never())->method('markAsFPCRequest');
        $this->assertFalse($this->plugin->afterLoad($this->subjectMock, false));
    }

    public function testAfterLoadFPC(): void
    {
        $result = $this->createMock(\Magento\Framework\App\Response\Http::class);
        $this->httpStorageMock->expects($this->once())->method('markAsFPCRequest');
        $this->assertEquals($result, $this->plugin->afterLoad($this->subjectMock, $result));
    }
}
