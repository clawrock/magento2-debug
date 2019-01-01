<?php

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\Collector\LayoutCollector;
use ClawRock\Debug\Model\ValueObject\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testObject()
    {
        $id = uniqid();
        $name = 'block name';
        $module = 'ClawRock_Debug';
        $renderTime = 3.41;
        $template = $module . '::test.phtml';
        $children = ['test child', 'test child 2'];
        $parentId = uniqid();

        $parentBlockMock = $this->getMockBuilder(\Magento\Framework\View\Element\Template::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parentBlockMock->expects($this->once())->method('getData')
            ->with(LayoutCollector::BLOCK_PROFILER_ID_KEY)
            ->willReturn($parentId);

        $blockMock = $this->getMockBuilder(\Magento\Framework\View\Element\Template::class)
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->exactly(2))->method('getData')
            ->withConsecutive([LayoutCollector::BLOCK_PROFILER_ID_KEY], [LayoutCollector::RENDER_TIME])
            ->willReturnOnConsecutiveCalls($id, $renderTime);
        $blockMock->expects($this->once())->method('getNameInLayout')->willReturn($name);
        $blockMock->expects($this->once())->method('getModuleName')->willReturn($module);
        $blockMock->expects($this->once())->method('getTemplate')->willReturn($template);
        $blockMock->expects($this->once())->method('getChildNames')->willReturn($children);
        $blockMock->expects($this->exactly(2))->method('getParentBlock')->willReturn($parentBlockMock);

        $object = new Block($blockMock);
        $this->assertEquals($id, $object->getId());
        $this->assertEquals($name, $object->getName());
        $this->assertEquals(get_class($blockMock), $object->getClass());
        $this->assertEquals($module, $object->getModule());
        $this->assertEquals($renderTime, $object->getRenderTime());
        $this->assertEquals($template, $object->getTemplate());
        $this->assertEquals($children, $object->getChildren());
        $this->assertEquals($parentId, $object->getParentId());

        $orphanBlockMock = $this->getMockBuilder(\Magento\Framework\View\Element\Template::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orphanBlockMock->expects($this->exactly(2))->method('getData')
            ->withConsecutive([LayoutCollector::BLOCK_PROFILER_ID_KEY], [LayoutCollector::RENDER_TIME])
            ->willReturnOnConsecutiveCalls($id, $renderTime);
        $orphanBlockMock->expects($this->once())->method('getNameInLayout')->willReturn($name);
        $orphanBlockMock->expects($this->once())->method('getModuleName')->willReturn($module);
        $orphanBlockMock->expects($this->once())->method('getTemplate')->willReturn($template);
        $orphanBlockMock->expects($this->once())->method('getChildNames')->willReturn($children);
        $orphanBlockMock->expects($this->once())->method('getParentBlock')->willReturn(null);

        $orphanObject = new Block($orphanBlockMock);
        $this->assertEquals($id, $orphanObject->getId());
        $this->assertEquals($name, $orphanObject->getName());
        $this->assertEquals(get_class($orphanBlockMock), $orphanObject->getClass());
        $this->assertEquals($module, $orphanObject->getModule());
        $this->assertEquals($renderTime, $orphanObject->getRenderTime());
        $this->assertEquals($template, $orphanObject->getTemplate());
        $this->assertEquals($children, $orphanObject->getChildren());
        $this->assertEquals('', $orphanObject->getParentId());
    }
}
