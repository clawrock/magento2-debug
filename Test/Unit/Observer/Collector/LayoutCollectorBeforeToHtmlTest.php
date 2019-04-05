<?php

namespace ClawRock\Debug\Test\Unit\Observer\Collector;

use ClawRock\Debug\Observer\Collector\LayoutCollectorBeforeToHtml;
use PHPUnit\Framework\TestCase;

class LayoutCollectorBeforeToHtmlTest extends TestCase
{
    private $blockMock;

    private $observerMock;

    private $observer;

    protected function setUp()
    {
        $this->blockMock = $this->getMockBuilder(\Magento\Framework\View\Element\AbstractBlock::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getBlock'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = new LayoutCollectorBeforeToHtml();
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->once())->method('getBlock')->willReturn($this->blockMock);
        $this->blockMock->expects($this->exactly(2))->method('addData');
        $this->blockMock->expects($this->exactly(2))->method('getParentBlock')->willReturn($this->blockMock);

        $this->observer->execute($this->observerMock);
    }
}
