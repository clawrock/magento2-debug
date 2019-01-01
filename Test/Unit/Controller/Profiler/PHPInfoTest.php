<?php

namespace ClawRock\Debug\Test\Unit\Controller\Profiler;

use ClawRock\Debug\Controller\Profiler\PHPInfo;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class PHPInfoTest extends TestCase
{
    public function testExecute()
    {
        $controller = (new ObjectManager($this))->getObject(PHPInfo::class);
        ob_start();
        $result = $controller->execute();
        ob_end_clean();
        $this->assertTrue($result);
    }
}
