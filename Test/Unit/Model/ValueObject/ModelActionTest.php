<?php

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\ModelAction;
use PHPUnit\Framework\TestCase;

class ModelActionTest extends TestCase
{
    public function testObject()
    {
        $name = 'name';
        $model = 'model';
        $trace = [1, 2, 3];
        $time = 5.21;

        $modelAction = new ModelAction($name, $model, $time, $trace);
        $this->assertEquals($name, $modelAction->getName());
        $this->assertEquals($model, $modelAction->getModel());
        $this->assertEquals($time, $modelAction->getTime());
        $this->assertEquals($trace, $modelAction->getTrace());
        $this->assertNotEmpty($modelAction->getTraceHash());
        $this->assertStringStartsWith($name, $modelAction->getId());
        $this->assertStringEndsWith($model, $modelAction->getId());

        $modelAction = new ModelAction($name, $model, $time, []);
        $this->assertEquals('', $modelAction->getTraceHash());
    }
}
