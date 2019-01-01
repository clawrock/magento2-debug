<?php

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\LayoutNode;
use PHPUnit\Framework\TestCase;

class LayoutNodeTest extends TestCase
{
    public function testObject()
    {
        $name = 'name';
        $class = 'class';
        $module = 'module';
        $blockRenderTime = 1.11;
        $template = 'template';
        $parentId = 'parent_id';

        $layoutRenderTime = 11.21;
        $prefix = 'prefix';
        $children = [1, 2, 3, 4];
        $blockMock = $this->getMockBuilder(\ClawRock\Debug\Model\ValueObject\Block::class)
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->once())->method('getName')->willReturn($name);
        $blockMock->expects($this->once())->method('getClass')->willReturn($class);
        $blockMock->expects($this->once())->method('getModule')->willReturn($module);
        $blockMock->expects($this->exactly(2))->method('getRenderTime')->willReturn($blockRenderTime);
        $blockMock->expects($this->once())->method('getTemplate')->willReturn($template);
        $blockMock->expects($this->once())->method('getParentId')->willReturn($parentId);

        $layoutNode = new LayoutNode($blockMock, $layoutRenderTime, $prefix, $children);
        $this->assertEquals($name, $layoutNode->getName());
        $this->assertEquals($class, $layoutNode->getClass());
        $this->assertEquals($module, $layoutNode->getModule());
        $this->assertEquals($blockRenderTime, $layoutNode->getRenderTime());
        $this->assertEquals($template, $layoutNode->getTemplate());
        $this->assertEquals($parentId, $layoutNode->getParentId());
        $this->assertEquals($children, $layoutNode->getChildren());
        $this->assertTrue($layoutNode->hasChildren());
        $this->assertEquals($prefix, $layoutNode->getPrefix());
        $this->assertEquals($blockRenderTime / $layoutRenderTime, $layoutNode->getRenderPercent());

        $layoutNode = new LayoutNode($blockMock, null, null, []);
        $this->assertEquals(0, $layoutNode->getRenderPercent());
    }
}
