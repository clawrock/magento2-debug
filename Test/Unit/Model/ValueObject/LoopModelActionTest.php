<?php

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\LoopModelAction;
use PHPUnit\Framework\TestCase;

class LoopModelActionTest extends TestCase
{
    public function testObject()
    {
        $name = 'name';
        $model = 'model';
        $trace = [1, 2, 3];
        $time = 5.21;
        $count = 3;
        $modelActionMock = $this->getMockBuilder(\ClawRock\Debug\Model\ValueObject\ModelAction::class)
            ->disableOriginalConstructor()
            ->getMock();

        $modelActionMock->expects($this->once())->method('getName')->willReturn($name);
        $modelActionMock->expects($this->once())->method('getModel')->willReturn($model);
        $modelActionMock->expects($this->once())->method('getTrace')->willReturn($trace);

        $loopAction = new LoopModelAction($modelActionMock, $time, $count);
        $this->assertEquals($modelActionMock, $loopAction->getModelAction());
        $this->assertEquals($name, $loopAction->getName());
        $this->assertEquals($model, $loopAction->getModel());
        $this->assertEquals($trace, $loopAction->getTrace());
        $this->assertEquals($time, $loopAction->getTime());
        $this->assertEquals($count, $loopAction->getCount());
    }
}
