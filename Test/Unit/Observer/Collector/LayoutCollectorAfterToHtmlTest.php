<?php

namespace ClawRock\Debug\Test\Unit\Observer\Collector;

use ClawRock\Debug\Observer\Collector\LayoutCollectorAfterToHtml;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class LayoutCollectorAfterToHtmlTest extends TestCase
{
    private $blockMock;

    private $layoutCollectorMock;

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

        $this->layoutCollectorMock = $this->getMockBuilder(\ClawRock\Debug\Model\Collector\LayoutCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = (new ObjectManager($this))->getObject(LayoutCollectorAfterToHtml::class, [
            'layoutCollector' => $this->layoutCollectorMock,
        ]);
    }

    public function testExecute()
    {
        $this->observerMock->expects($this->once())->method('getBlock')->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('addData');
        $this->layoutCollectorMock->expects($this->once())->method('log');

        $this->observer->execute($this->observerMock);
    }
}
