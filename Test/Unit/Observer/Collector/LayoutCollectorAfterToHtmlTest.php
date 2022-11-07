<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Observer\Collector;

use ClawRock\Debug\Observer\Collector\LayoutCollectorAfterToHtml;
use PHPUnit\Framework\TestCase;

class LayoutCollectorAfterToHtmlTest extends TestCase
{
    /** @var \Magento\Framework\View\Element\AbstractBlock&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\View\Element\AbstractBlock $blockMock;
    /** @var \ClawRock\Debug\Model\Collector\LayoutCollector&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Collector\LayoutCollector $layoutCollectorMock;
    /** @var \Magento\Framework\Event\Observer&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Event\Observer $observerMock;
    private \ClawRock\Debug\Observer\Collector\LayoutCollectorAfterToHtml $observer;

    protected function setUp(): void
    {
        $this->blockMock = $this->getMockBuilder(\Magento\Framework\View\Element\AbstractBlock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->blockMock->expects($this->any())->method('getNameInLayout')->willReturn('block_name');
        $this->blockMock->expects($this->any())->method('getModuleName')->willReturn('module_name');
        $this->blockMock->expects($this->any())->method('getChildNames')->willReturn([]);

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->layoutCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\LayoutCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new LayoutCollectorAfterToHtml($this->layoutCollectorMock);
    }

    public function testExecute(): void
    {
        $this->observerMock->expects($this->once())->method('getBlock')->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('addData');
        $this->layoutCollectorMock->expects($this->once())->method('log');

        $this->observer->execute($this->observerMock);
    }
}
