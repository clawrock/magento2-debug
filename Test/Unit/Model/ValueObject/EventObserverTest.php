<?php

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\EventObserver;
use PHPUnit\Framework\TestCase;

class EventObserverTest extends TestCase
{
    public function testObject()
    {
        $name = 'observer';
        $class = 'class';
        $event = 'event';
        $time = 9.43;

        $eventObserver = new EventObserver($name, $class, $event, $time);

        $this->assertEquals($name, $eventObserver->getName());
        $this->assertEquals($class, $eventObserver->getClass());
        $this->assertEquals($event, $eventObserver->getEvent());
        $this->assertEquals($time, $eventObserver->getTime());
        $this->assertStringStartsWith($name, $eventObserver->getId());
    }
}
