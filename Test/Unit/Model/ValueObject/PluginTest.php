<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\ValueObject;

use ClawRock\Debug\Model\ValueObject\Plugin;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testObject(): void
    {
        $class = 'class';
        $name = 'name';
        $sortOrder = 1;
        $method = 'method';
        $type = 'type';

        $plugin = new Plugin($class, $name, $sortOrder, $method, $type);

        $this->assertEquals($class, $plugin->getClass());
        $this->assertEquals($name, $plugin->getName());
        $this->assertEquals($sortOrder, $plugin->getSortOrder());
        $this->assertEquals($method, $plugin->getMethod());
        $this->assertEquals($type, $plugin->getType());
    }
}
